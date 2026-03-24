<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Exports\LeaveRequestsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isCommander = in_array($user->role, [
            \App\Enums\UserRole::ADMIN->value,
            \App\Enums\UserRole::DIRECTOR->value,
            \App\Enums\UserRole::DEPUTY_DIRECTOR->value,
            \App\Enums\UserRole::DEPARTMENT_HEAD->value,
        ]);

        // Define a base filtering query for consistency across all statistics
        $applyFilters = function ($q) use ($request, $isCommander, $user) {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $q->where(function ($sub) use ($request) {
                    $sub->whereDate('start_date', '<=', $request->end_date)
                        ->whereDate('end_date', '>=', $request->start_date);
                });
            } elseif ($request->filled('start_date')) {
                $q->whereDate('end_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $q->whereDate('start_date', '<=', $request->end_date);
            }

            if ($request->filled('department')) {
                $q->whereHas('user', function ($sub) use ($request) {
                    $sub->where('department', $request->department);
                });
            } elseif (!$isCommander) {
                $q->whereHas('user', function ($sub) use ($user) {
                    $sub->where('department', $user->department);
                });
            }

            if ($request->filled('leave_type_id')) {
                $q->where('leave_type_id', $request->leave_type_id);
            }

            if ($request->filled('status')) {
                $q->where('status', $request->status);
            } else {
                // Default to excluding cancelled and rejected for statistics if no specific status is filtered
                $q->whereNotIn('status', ['cancelled', 'rejected']);
            }
        };

        // 1. Data Table Query
        $query = LeaveRequest::with(['user', 'leaveType'])
            ->whereHas('leaveType', function ($q) {
                $q->where('slug', '!=', 'temporary');
            });

        // Apply filters to table query
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('start_date', '<=', $request->end_date)
                    ->whereDate('end_date', '>=', $request->start_date);
            });
        } elseif ($request->filled('start_date')) {
            $query->whereDate('end_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('start_date', '<=', $request->end_date);
        }

        if ($request->filled('department')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        } elseif (!$isCommander) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department', $user->department);
            });
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('start_date', 'desc')->paginate(15)->withQueryString();
        $requests->through(function ($r) {
            $r->start_date_thai = $r->start_date->locale('th')->translatedFormat('j M Y');
            $r->end_date_thai   = $r->end_date->locale('th')->translatedFormat('j M Y');
            return $r;
        });

        // Data for Filters
        if ($isCommander) {
            $departments = \App\Models\Department::all();
        } else {
            $departments = \App\Models\Department::where('name', $user->department)->get();
        }
        $leaveTypes = LeaveType::all();

        // 2. Statistics: Top Leave Takers
        $topLeaversQuery = LeaveRequest::selectRaw('user_id, SUM(total_days) as total_leave_days, COUNT(*) as leave_count')
            ->whereHas('leaveType', function ($q) {
                $q->where('slug', '!=', 'official-duty')
                    ->where('slug', '!=', 'temporary');
            })
            ->groupBy('user_id')
            ->orderByDesc('total_leave_days')
            ->limit(5);

        $applyFilters($topLeaversQuery);
        $topLeavers = $topLeaversQuery->with('user')->get();

        // 3. Statistics: Most Popular Leave Types
        $popularLeaveTypesQuery = LeaveRequest::selectRaw('leave_type_id, SUM(total_days) as total_days, COUNT(*) as usage_count')
            ->whereHas('leaveType', function ($q) {
                $q->where('slug', '!=', 'temporary');
            })
            ->groupBy('leave_type_id')
            ->orderByDesc('usage_count');

        $applyFilters($popularLeaveTypesQuery);
        $popularLeaveTypes = $popularLeaveTypesQuery->with('leaveType')->get();

        // 4. Total statistics for stat cards
        $totalApprovedLeavesQuery = LeaveRequest::whereHas('leaveType', function ($q) {
            $q->where('slug', '!=', 'temporary');
        });
        $applyFilters($totalApprovedLeavesQuery);
        $totalApprovedLeaves = $totalApprovedLeavesQuery->count();

        // 5. Department breakdown
        $deptBreakdownQuery = LeaveRequest::with(['user', 'leaveType'])
            ->whereHas('leaveType', function ($q) {
                $q->where('slug', '!=', 'temporary')
                  ->where('slug', '!=', 'official-duty');
            });
        $applyFilters($deptBreakdownQuery);
        $allApprovedLeaves = $deptBreakdownQuery->get();

        $departmentStats = $allApprovedLeaves
            ->groupBy(fn($r) => $r->user->department ?? 'ไม่ระบุ')
            ->map(function ($leaves, $deptName) {
                $totalDays  = $leaves->sum('total_days');
                $totalCount = $leaves->count();

                // Top leaver in this department
                $topLeaver = $leaves->groupBy('user_id')
                    ->map(fn($g) => ['user' => $g->first()->user, 'days' => $g->sum('total_days'), 'count' => $g->count()])
                    ->sortByDesc('days')
                    ->first();

                // Leave type distribution in this department
                $leaveTypeBreakdown = $leaves->groupBy(fn($r) => $r->leaveType->name ?? 'ไม่ระบุ')
                    ->map(fn($g) => ['count' => $g->count(), 'days' => $g->sum('total_days')])
                    ->sortByDesc('days')
                    ->take(4);

                // Per-person ranking
                $personRanking = $leaves->groupBy('user_id')
                    ->map(fn($g) => [
                        'user'  => $g->first()->user,
                        'days'  => $g->sum('total_days'),
                        'count' => $g->count(),
                        'types' => $g->groupBy(fn($r) => $r->leaveType->name ?? 'ไม่ระบุ')
                                     ->map(fn($tg) => $tg->sum('total_days'))
                                     ->sortByDesc(fn($v) => $v),
                    ])
                    ->sortByDesc('days')
                    ->take(5)
                    ->values();

                return [
                    'name'              => $deptName,
                    'total_days'        => $totalDays,
                    'total_count'       => $totalCount,
                    'top_leaver'        => $topLeaver,
                    'leave_type_breakdown' => $leaveTypeBreakdown,
                    'person_ranking'    => $personRanking,
                ];
            })
            ->sortByDesc('total_days')
            ->values();

        // 6. Monthly trend (stays for the current year or respects the year filter)
        $currentYear = $request->filled('start_date') ? \Carbon\Carbon::parse($request->start_date)->year : now()->year;
        $monthlyTrendQuery = LeaveRequest::selectRaw('MONTH(start_date) as month, COUNT(*) as count, SUM(total_days) as total_days')
            ->whereYear('start_date', $currentYear)
            ->whereHas('leaveType', function ($q) {
                $q->where('slug', '!=', 'temporary');
            })
            ->groupBy('month')
            ->orderBy('month');
            
        // For monthly trend, we only apply department/leave_type/status filters, not the date range itself which is monthly
        if ($request->filled('department')) {
            $monthlyTrendQuery->whereHas('user', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        } elseif (!$isCommander) {
            $monthlyTrendQuery->whereHas('user', function ($q) use ($user) {
                $q->where('department', $user->department);
            });
        }
        if ($request->filled('leave_type_id')) {
            $monthlyTrendQuery->where('leave_type_id', $request->leave_type_id);
        }
        if ($request->filled('status')) {
            $monthlyTrendQuery->where('status', $request->status);
        } else {
            $monthlyTrendQuery->whereNotIn('status', ['cancelled', 'rejected']);
        }

        $monthlyRaw = $monthlyTrendQuery->get()->keyBy('month');
        $monthlyTrend = collect(range(1, 12))->map(fn($m) => [
            'month'      => $m,
            'count'      => $monthlyRaw->get($m)?->count ?? 0,
            'total_days' => $monthlyRaw->get($m)?->total_days ?? 0,
        ]);

        return Inertia::render('Reports/Index', compact(
            'requests', 'departments', 'leaveTypes',
            'topLeavers', 'popularLeaveTypes', 'totalApprovedLeaves',
            'departmentStats', 'monthlyTrend', 'currentYear'
        ));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'department', 'leave_type_id', 'status']);
        return Excel::download(new LeaveRequestsExport($filters), 'leave-report-' . now()->format('Y-m-d') . '.xlsx');
    }
}
