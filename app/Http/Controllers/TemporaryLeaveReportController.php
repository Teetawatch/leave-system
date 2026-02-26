<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Department;
use Illuminate\Http\Request;

class TemporaryLeaveReportController extends Controller
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

        // Get the temporary leave type
        $temporaryLeaveType = LeaveType::where('slug', 'temporary')->first();

        // Base query for temporary leave only
        $query = LeaveRequest::with(['user', 'leaveType', 'approvals.approver'])
            ->whereHas('leaveType', function ($q) {
                $q->where('slug', 'temporary');
            });

        // Default Filter for Non-Commanders
        if (!$isCommander) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department', $user->department);
            });
        }

        // Filter Logic - Use overlapping date range
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

        // Filter by department
        if ($request->filled('department')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        // Filter by period (morning/afternoon)
        if ($request->filled('period')) {
            $query->where('temporary_leave_period', $request->period);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->whereIn('status', ['pending_supervisor', 'pending_head', 'pending_deputy_director', 'pending_manager', 'pending_director']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Data for Filters
        if ($isCommander) {
            $departments = Department::all();
        } else {
            $departments = Department::where('name', $user->department)->get();
        }

        // Statistics
        $totalTemporaryLeaves = LeaveRequest::whereHas('leaveType', fn($q) => $q->where('slug', 'temporary'));
        $approvedCount = (clone $totalTemporaryLeaves)->where('status', 'approved')->count();
        $pendingCount = (clone $totalTemporaryLeaves)->whereIn('status', ['pending_supervisor', 'pending_head', 'pending_deputy_director', 'pending_manager', 'pending_director'])->count();
        $morningCount = (clone $totalTemporaryLeaves)->where('temporary_leave_period', 'morning')->count();
        $afternoonCount = (clone $totalTemporaryLeaves)->where('temporary_leave_period', 'afternoon')->count();
        $totalCount = $totalTemporaryLeaves->count();

        return view('reports.temporary-leave', compact(
            'requests',
            'departments',
            'totalCount',
            'approvedCount',
            'pendingCount',
            'morningCount',
            'afternoonCount'
        ));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'department', 'period', 'status']);
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TemporaryLeaveExport($filters), 
            'temporary-leave-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
