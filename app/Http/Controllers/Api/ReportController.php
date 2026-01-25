<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\GuardChangeRequest;
use App\Models\Department;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function leaveSummary(Request $request)
    {
        $user = auth()->user();

        // Allowed roles: admin, director, deputy_director, department_head, supervisor, manager
        // (Adjusted based on standard commander roles in previous controllers)
        $isCommander = in_array($user->role, [
            \App\Enums\UserRole::ADMIN->value,
            \App\Enums\UserRole::DIRECTOR->value,
            \App\Enums\UserRole::DEPUTY_DIRECTOR->value,
            \App\Enums\UserRole::DEPARTMENT_HEAD->value,
        ]);

        if (!$isCommander) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = LeaveRequest::with(['user', 'leaveType']);

        // Default Filter for Non-Admins/Directors (e.g. Department Heads)
        $isHighLevelAdmin = in_array($user->role, [
            \App\Enums\UserRole::ADMIN->value,
            \App\Enums\UserRole::DIRECTOR->value,
            \App\Enums\UserRole::DEPUTY_DIRECTOR->value,
        ]);

        if (!$isHighLevelAdmin) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department', $user->department);
            });
        }

        // Filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('start_date', '<=', $request->end_date)
                    ->whereDate('end_date', '>=', $request->start_date);
            });
        }

        if ($request->filled('department')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->limit(100)->get();

        // Stats
        $stats = [
            'total_approved_leaves' => (clone $query)->where('status', 'approved')->count(),
            'total_pending' => (clone $query)->whereIn('status', ['pending', 'pending_supervisor', 'pending_head', 'pending_manager'])->count(),
        ];

        return response()->json([
            'requests' => $requests,
            'stats' => $stats,
            'departments' => Department::all()->pluck('name'),
            'leave_types' => LeaveType::all(),
        ]);
    }

    public function guardChangeSummary(Request $request)
    {
        $user = auth()->user();

        // Allowed roles: admin, director, deputy_director
        $allowedRoles = [
            \App\Enums\UserRole::ADMIN->value,
            \App\Enums\UserRole::DIRECTOR->value,
            \App\Enums\UserRole::DEPUTY_DIRECTOR->value,
        ];

        if (!in_array($user->role, $allowedRoles)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = GuardChangeRequest::with(['user', 'replacementUser', 'approver']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('duty_date', '>=', $request->start_date)
                ->whereDate('duty_date', '<=', $request->end_date);
        }

        if ($request->filled('department')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->limit(100)->get();

        $stats = [
            'total' => (clone $query)->count(),
            'approved' => (clone $query)->where('status', 'fully_approved')->count(),
            'pending' => (clone $query)->whereIn('status', ['pending', 'approved', 'director_approved'])->count(),
        ];

        return response()->json([
            'requests' => $requests,
            'stats' => $stats,
            'departments' => Department::all()->pluck('name'),
        ]);
    }
}
