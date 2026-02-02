<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\GuardChangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Display the shared calendar
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get all departments for filter
        $departments = User::whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->filter()
            ->sort()
            ->values();

        // Get leave types with colors
        $leaveTypes = \App\Models\LeaveType::all();

        return view('calendar.index', compact('departments', 'leaveTypes'));
    }

    /**
     * Get calendar events (AJAX endpoint)
     */
    public function events(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);
        $department = $request->department;
        $showGuardChange = $request->boolean('show_guard_change', true);

        $events = [];

        // Get approved leave requests
        $leaveQuery = LeaveRequest::with(['user', 'leaveType'])
            ->where('status', 'approved')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            });

        // Filter by department if specified
        if ($department && $department !== 'all') {
            $leaveQuery->whereHas('user', function ($query) use ($department) {
                $query->where('department', $department);
            });
        }

        // Exclude 'ไปราชการ' (official-duty)
        $leaveQuery->whereHas('leaveType', function ($query) {
            $query->where('slug', '!=', 'official-duty');
        });

        $leaveRequests = $leaveQuery->get();

        // Define colors for leave types
        $leaveColors = [
            'vacation' => '#10B981', // Green - พักร้อน
            'sick' => '#EF4444',     // Red - ลาป่วย
            'personal' => '#F59E0B', // Yellow - ลากิจ
            'maternity' => '#EC4899', // Pink - ลาคลอด
            'ordination' => '#8B5CF6', // Purple - ลาบวช
            'military' => '#6366F1', // Indigo - ลาเกณฑ์ทหาร
            'training' => '#06B6D4', // Cyan - ลาอบรม
            'default' => '#6B7280',   // Gray - อื่นๆ
        ];

        foreach ($leaveRequests as $leave) {
            $color = $leaveColors[$leave->leaveType->slug ?? 'default'] ?? $leaveColors['default'];
            $userName = $leave->user->rank ? $leave->user->rank . ' ' . $leave->user->name : $leave->user->name;

            $events[] = [
                'id' => 'leave_' . $leave->id,
                'title' => $userName . ' - ' . $leave->leaveType->name,
                'start' => $leave->start_date->format('Y-m-d'),
                'end' => $leave->end_date->addDay()->format('Y-m-d'), // FullCalendar end is exclusive
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'leave',
                    'leaveType' => $leave->leaveType->name,
                    'leaveTypeSlug' => $leave->leaveType->slug,
                    'userId' => $leave->user_id,
                    'userName' => $userName,
                    'department' => $leave->user->department,
                    'reason' => $leave->reason,
                    'totalDays' => $leave->total_days,
                    'startDate' => $leave->start_date->format('d/m/Y'),
                    'endDate' => $leave->end_date->format('d/m/Y'),
                ],
            ];
        }

        // Get approved guard change requests if enabled
        if ($showGuardChange) {
            $guardQuery = GuardChangeRequest::with(['user', 'replacementUser'])
                ->where('status', 'approved')
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('duty_date', [$start, $end]);
                });

            if ($department && $department !== 'all') {
                $guardQuery->whereHas('user', function ($query) use ($department) {
                    $query->where('department', $department);
                });
            }

            $guardChanges = $guardQuery->get();

            foreach ($guardChanges as $guard) {
                $userName = $guard->user->rank ? $guard->user->rank . ' ' . $guard->user->name : $guard->user->name;

                // Guard change event
                $events[] = [
                    'id' => 'guard_' . $guard->id,
                    'title' => '🔄 ' . $userName . ' (เปลี่ยนเวร)',
                    'start' => Carbon::parse($guard->duty_date)->format('Y-m-d'),
                    'backgroundColor' => '#94A3B8',
                    'borderColor' => '#64748B',
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'guard_change',
                        'userId' => $guard->user_id,
                        'userName' => $userName,
                        'department' => $guard->user->department,
                        'originalDate' => Carbon::parse($guard->duty_date)->format('d/m/Y'),
                        'newDate' => Carbon::parse($guard->duty_date)->format('d/m/Y'),
                        'substituteUser' => $guard->replacementUser ? $guard->replacementUser->name : null,
                        'reason' => $guard->remarks,
                        'dutyPosition' => $guard->duty_position,
                    ],
                ];
            }
        }

        return response()->json($events);
    }

    /**
     * Get summary statistics for a date range
     */
    public function summary(Request $request)
    {
        $start = Carbon::parse($request->start ?? now()->startOfMonth());
        $end = Carbon::parse($request->end ?? now()->endOfMonth());
        $department = $request->department;

        $query = LeaveRequest::where('status', 'approved')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end]);
            });

        if ($department && $department !== 'all') {
            $query->whereHas('user', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        // Exclude 'ไปราชการ' (official-duty)
        $query->whereHas('leaveType', function ($q) {
            $q->where('slug', '!=', 'official-duty');
        });

        $leaveRequests = $query->with(['user', 'leaveType'])->get();

        // Count by leave type
        $byType = $leaveRequests->groupBy('leaveType.name')
            ->map(fn($group) => $group->count());

        // Count by department
        $byDepartment = $leaveRequests->groupBy('user.department')
            ->map(fn($group) => $group->count());

        // Total people on leave today
        $today = now()->format('Y-m-d');
        $onLeaveToday = LeaveRequest::where('status', 'approved')
            ->whereHas('leaveType', function ($q) {
                $q->where('slug', '!=', 'official-duty');
            })
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        return response()->json([
            'byType' => $byType,
            'byDepartment' => $byDepartment,
            'onLeaveToday' => $onLeaveToday,
            'totalRequests' => $leaveRequests->count(),
        ]);
    }
}
