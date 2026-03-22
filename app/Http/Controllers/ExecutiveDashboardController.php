<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class ExecutiveDashboardController extends Controller
{
    /**
     * Display the executive dashboard overview
     */
    public function index()
    {
        $user = Auth::user();

        // Verify user has executive role
        if (!in_array($user->role, ['admin', 'deputy_director', 'director'])) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Get all departments
        $departments = User::whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->filter()
            ->values();

        // 1. Overall Statistics
        $totalEmployees = User::where('is_registered', true)
            ->where('registration_status', 'approved')
            ->count();

        $totalLeaveRequests = LeaveRequest::whereYear('created_at', $currentYear)->count();
        $approvedLeaves = LeaveRequest::whereYear('created_at', $currentYear)->where('status', 'approved')->count();
        $pendingLeaves = LeaveRequest::whereYear('created_at', $currentYear)->whereIn('status', [
            'pending_supervisor',
            'pending_head',
            'pending_manager',
            'pending_deputy_director',
            'pending_director'
        ])->count();
        $rejectedLeaves = LeaveRequest::whereYear('created_at', $currentYear)->where('status', 'rejected')->count();

        // 2. Leave by Department (for chart)
        $leaveByDepartment = User::select('department')
            ->selectRaw('COUNT(DISTINCT users.id) as total_employees')
            ->join('leave_requests', 'users.id', '=', 'leave_requests.user_id')
            ->whereYear('leave_requests.created_at', $currentYear)
            ->where('leave_requests.status', 'approved')
            ->whereNotNull('users.department')
            ->groupBy('users.department')
            ->get()
            ->map(function ($item) use ($currentYear) {
                $totalDays = LeaveRequest::join('users', 'leave_requests.user_id', '=', 'users.id')
                    ->where('users.department', $item->department)
                    ->whereYear('leave_requests.created_at', $currentYear)
                    ->where('leave_requests.status', 'approved')
                    ->sum('leave_requests.total_days');

                return [
                    'department' => $item->department,
                    'total_days' => $totalDays,
                    'total_employees' => User::where('department', $item->department)->count(),
                    'avg_days' => $item->total_employees > 0 ? round($totalDays / $item->total_employees, 1) : 0,
                ];
            });

        // 3. Monthly Leave Trend (last 12 months)
        $monthlyTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $leaveData = LeaveRequest::whereBetween('start_date', [$monthStart, $monthEnd])
                ->where('status', 'approved')
                ->selectRaw('leave_type_id, COUNT(*) as count, SUM(total_days) as total_days')
                ->groupBy('leave_type_id')
                ->get()
                ->keyBy('leave_type_id');

            $monthlyTrend->push([
                'month' => $date->translatedFormat('M'),
                'year' => $date->year,
                'month_year' => $date->translatedFormat('M Y'),
                'vacation' => $leaveData->get(LeaveType::where('slug', 'vacation')->first()?->id)?->total_days ?? 0,
                'sick' => $leaveData->get(LeaveType::where('slug', 'sick')->first()?->id)?->total_days ?? 0,
                'personal' => $leaveData->get(LeaveType::where('slug', 'personal')->first()?->id)?->total_days ?? 0,
                'total_requests' => LeaveRequest::whereBetween('start_date', [$monthStart, $monthEnd])
                    ->where('status', 'approved')
                    ->count(),
            ]);
        }

        // 4. Top Leave Takers (Employees who take leave frequently)
        $officialDutyTypeId = LeaveType::where('slug', 'official-duty')->first()?->id;

        $topLeaveTakersQuery = User::select('users.id', 'users.name', 'users.rank', 'users.department')
            ->selectRaw('COUNT(leave_requests.id) as leave_count')
            ->selectRaw('SUM(leave_requests.total_days) as total_days')
            ->join('leave_requests', 'users.id', '=', 'leave_requests.user_id')
            ->whereYear('leave_requests.created_at', $currentYear)
            ->where('leave_requests.status', 'approved');

        if ($officialDutyTypeId) {
            $topLeaveTakersQuery->where('leave_requests.leave_type_id', '!=', $officialDutyTypeId);
        }

        $topLeaveTakers = $topLeaveTakersQuery->groupBy('users.id', 'users.name', 'users.rank', 'users.department')
            ->orderByDesc('total_days')
            ->limit(10)
            ->get();

        // 5. Leave Type Distribution
        $leaveTypeDistribution = LeaveType::select('leave_types.id', 'leave_types.name', 'leave_types.slug')
            ->selectRaw('COUNT(leave_requests.id) as request_count')
            ->selectRaw('COALESCE(SUM(leave_requests.total_days), 0) as total_days')
            ->leftJoin('leave_requests', function ($join) use ($currentYear) {
                $join->on('leave_types.id', '=', 'leave_requests.leave_type_id')
                    ->whereYear('leave_requests.created_at', $currentYear)
                    ->where('leave_requests.status', 'approved');
            })
            ->groupBy('leave_types.id', 'leave_types.name', 'leave_types.slug')
            ->get();

        // 6. Today's Leave Summary
        $todayOnLeave = LeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->with(['user', 'leaveType'])
            ->get()
            ->map(fn ($r) => [
                'id'         => $r->id,
                'user'       => $r->user ? ['name' => $r->user->name, 'rank' => $r->user->rank, 'department' => $r->user->department, 'avatar' => $r->user->avatar] : null,
                'leave_type' => $r->leaveType ? ['name' => $r->leaveType->name, 'slug' => $r->leaveType->slug] : null,
                'start_date'       => $r->start_date->format('Y-m-d'),
                'end_date'         => $r->end_date->format('Y-m-d'),
                'start_date_thai'  => $r->start_date->locale('th')->translatedFormat('j M Y'),
                'end_date_thai'    => $r->end_date->locale('th')->translatedFormat('j M Y'),
                'total_days'       => $r->total_days + 0,
                'status'           => $r->status,
            ]);

        // 7. Recent Leave Requests (for quick review)
        $recentRequests = LeaveRequest::with(['user', 'leaveType'])
            ->whereIn('status', [
                'pending_supervisor',
                'pending_head',
                'pending_manager',
                'pending_deputy_director',
                'pending_director'
            ])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'id'         => $r->id,
                'user'       => $r->user ? ['name' => $r->user->name, 'rank' => $r->user->rank, 'department' => $r->user->department, 'avatar' => $r->user->avatar] : null,
                'leave_type' => $r->leaveType ? ['name' => $r->leaveType->name, 'slug' => $r->leaveType->slug] : null,
                'start_date'       => $r->start_date->format('Y-m-d'),
                'end_date'         => $r->end_date->format('Y-m-d'),
                'start_date_thai'  => $r->start_date->locale('th')->translatedFormat('j M Y'),
                'end_date_thai'    => $r->end_date->locale('th')->translatedFormat('j M Y'),
                'total_days'       => $r->total_days + 0,
                'status'           => $r->status,
                'created_at_human' => $r->created_at->diffForHumans(),
            ]);

        // 8. Department-wise employee count
        $departmentStats = User::select('department')
            ->selectRaw('COUNT(*) as employee_count')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->groupBy('department')
            ->orderByDesc('employee_count')
            ->get();

        // 9. This month vs last month comparison
        $thisMonthLeaves = LeaveRequest::whereYear('start_date', $currentYear)
            ->whereMonth('start_date', $currentMonth)
            ->where('status', 'approved')
            ->count();

        $lastMonthLeaves = LeaveRequest::whereYear('start_date', now()->subMonth()->year)
            ->whereMonth('start_date', now()->subMonth()->month)
            ->where('status', 'approved')
            ->count();

        $leaveChangePercent = $lastMonthLeaves > 0
            ? round((($thisMonthLeaves - $lastMonthLeaves) / $lastMonthLeaves) * 100, 1)
            : 0;

        return Inertia::render('Executive/Dashboard', compact(
            'totalEmployees',
            'totalLeaveRequests',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves',
            'leaveByDepartment',
            'monthlyTrend',
            'topLeaveTakers',
            'leaveTypeDistribution',
            'todayOnLeave',
            'recentRequests',
            'departmentStats',
            'departments',
            'thisMonthLeaves',
            'lastMonthLeaves',
            'leaveChangePercent',
            'currentYear'
        ));
    }

    /**
     * API endpoint for department filter
     */
    public function departmentStats(Request $request)
    {
        $department = $request->get('department');
        $currentYear = now()->year;

        $query = LeaveRequest::whereYear('created_at', $currentYear)
            ->where('status', 'approved');

        if ($department && $department !== 'all') {
            $query->whereHas('user', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $stats = $query->selectRaw('leave_type_id, COUNT(*) as count, SUM(total_days) as total_days')
            ->groupBy('leave_type_id')
            ->with('leaveType')
            ->get();

        return response()->json($stats);
    }

    /**
     * Export dashboard data as PDF
     */
    public function exportPdf(Request $request)
    {
        // Implementation for PDF export
        // Similar to index() but returns PDF
    }
}
