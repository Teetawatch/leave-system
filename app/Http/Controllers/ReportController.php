<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Exports\LeaveRequestsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

        $query = LeaveRequest::with(['user', 'leaveType']);

        // Default Filter for Non-Commanders
        if (!$isCommander) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department', $user->department);
            });
        }

        // Filter Logic - Use overlapping date range to find all leave requests within the period
        if ($request->filled('start_date') && $request->filled('end_date')) {
            // Find all leave requests that overlap with the selected date range
            $query->where(function ($q) use ($request) {
                $q->whereDate('start_date', '<=', $request->end_date)
                    ->whereDate('end_date', '>=', $request->start_date);
            });
        } elseif ($request->filled('start_date')) {
            // Only start_date specified: find leaves that end on or after this date
            $query->whereDate('end_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            // Only end_date specified: find leaves that start on or before this date
            $query->whereDate('start_date', '<=', $request->end_date);
        }
        if ($request->filled('department')) {
            // Allow filtering by specific department if they have access (Commanders) or if it's their own
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }
        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('start_date', 'desc')->paginate(15)->withQueryString();

        // Data for Filters
        if ($isCommander) {
            $departments = \App\Models\Department::all();
        } else {
            $departments = \App\Models\Department::where('name', $user->department)->get();
        }

        $leaveTypes = LeaveType::all();

        // Statistics: Top Leave Takers (all leaves except official-duty)
        $topLeaversQuery = LeaveRequest::selectRaw('user_id, SUM(total_days) as total_leave_days, COUNT(*) as leave_count')
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->whereHas('leaveType', function ($q) {
                $q->where('slug', '!=', 'official-duty');
            })
            ->groupBy('user_id')
            ->orderByDesc('total_leave_days')
            ->limit(5);

        if (!$isCommander) {
            $topLeaversQuery->whereHas('user', function ($q) use ($user) {
                $q->where('department', $user->department);
            });
        }

        $topLeavers = $topLeaversQuery->with('user')->get();

        // Statistics: Most Popular Leave Types (all leaves)
        $popularLeaveTypesQuery = LeaveRequest::selectRaw('leave_type_id, SUM(total_days) as total_days, COUNT(*) as usage_count')
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->groupBy('leave_type_id')
            ->orderByDesc('usage_count');

        if (!$isCommander) {
            $popularLeaveTypesQuery->whereHas('user', function ($q) use ($user) {
                $q->where('department', $user->department);
            });
        }

        $popularLeaveTypes = $popularLeaveTypesQuery->with('leaveType')->get();

        // Total statistics
        $totalApprovedLeaves = LeaveRequest::whereNotIn('status', ['cancelled', 'rejected']);
        if (!$isCommander) {
            $totalApprovedLeaves->whereHas('user', function ($q) use ($user) {
                $q->where('department', $user->department);
            });
        }
        $totalApprovedLeaves = $totalApprovedLeaves->count();

        return view('reports.index', compact('requests', 'departments', 'leaveTypes', 'topLeavers', 'popularLeaveTypes', 'totalApprovedLeaves'));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'department', 'leave_type_id', 'status']);
        return Excel::download(new LeaveRequestsExport($filters), 'leave-report-' . now()->format('Y-m-d') . '.xlsx');
    }
}
