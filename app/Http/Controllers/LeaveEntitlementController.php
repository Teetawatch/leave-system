<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveEntitlementController extends Controller
{
    /**
     * Display a listing of employees with their vacation leave entitlements.
     */
    public function index(Request $request)
    {
        $query = User::with(['leaveBalances' => function ($q) {
            $vacationType = LeaveType::where('slug', 'vacation')->first();
            if ($vacationType) {
                $q->where('leave_type_id', $vacationType->id)
                  ->where('year', date('Y'));
            }
        }]);

        // Filter by department
        if ($request->has('department') && $request->department !== '') {
            $query->where('department', $request->department);
        }

        // Search by name
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('name')->paginate(20)->withQueryString();
        
        // Get vacation type for reference
        $vacationType = LeaveType::where('slug', 'vacation')->first();
        
        // Get all departments for filter dropdown
        $departments = Department::orderBy('name')->get();
        
        $currentYear = date('Y');
        
        // Pre-process employees to include vacation balance with actual used days from approved requests
        foreach ($employees as $employee) {
            $balance = $employee->leaveBalances->first();
            
            // Calculate actual used days from approved leave requests
            $actualUsedDays = 0;
            if ($vacationType) {
                $actualUsedDays = LeaveRequest::where('user_id', $employee->id)
                    ->where('leave_type_id', $vacationType->id)
                    ->where('status', 'approved')
                    ->whereYear('start_date', $currentYear)
                    ->sum('total_days');
            }
            
            $totalDays = $balance ? $balance->total_days : 10;
            
            $employee->vacation_total = $totalDays;
            $employee->vacation_used = $actualUsedDays;
            $employee->vacation_remaining = max(0, $totalDays - $actualUsedDays);
            $employee->balance_id = $balance ? $balance->id : null;
        }

        return Inertia::render('LeaveEntitlements/Index', compact('employees', 'departments', 'vacationType'));
    }

    /**
     * Bulk update vacation leave entitlements for multiple employees.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'entitlements' => 'required|array',
            'entitlements.*.user_id' => 'required|exists:users,id',
            'entitlements.*.total_days' => 'required|numeric|min:0|max:30',
        ]);

        $vacationType = LeaveType::where('slug', 'vacation')->first();
        
        if (!$vacationType) {
            return redirect()->back()->with('error', 'ไม่พบประเภทลาพักผ่อนในระบบ');
        }

        $updatedCount = 0;
        $currentYear = date('Y');

        foreach ($request->entitlements as $entitlement) {
            $userId = $entitlement['user_id'];
            $newTotalDays = $entitlement['total_days'];

            // Calculate actual used days from approved leave requests
            $actualUsedDays = LeaveRequest::where('user_id', $userId)
                ->where('leave_type_id', $vacationType->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $currentYear)
                ->sum('total_days');

            // Calculate remaining days based on actual used days
            $remainingDays = max(0, $newTotalDays - $actualUsedDays);

            // Find existing balance or create new one
            $balance = LeaveBalance::where('user_id', $userId)
                ->where('leave_type_id', $vacationType->id)
                ->where('year', $currentYear)
                ->first();

            if ($balance) {
                // Update existing balance with actual used days
                $balance->total_days = $newTotalDays;
                $balance->used_days = $actualUsedDays;
                $balance->remaining_days = $remainingDays;
                $balance->save();
            } else {
                // Create new balance record with actual used days
                LeaveBalance::create([
                    'user_id' => $userId,
                    'leave_type_id' => $vacationType->id,
                    'year' => $currentYear,
                    'total_days' => $newTotalDays,
                    'used_days' => $actualUsedDays,
                    'remaining_days' => $remainingDays,
                ]);
            }

            $updatedCount++;
        }

        return redirect()->route('leave-entitlements.index')
            ->with('success', "อัพเดทสิทธิ์วันลาพักผ่อนเรียบร้อยแล้ว {$updatedCount} คน (คำนวณวันลาที่ใช้ไปแล้วจากใบลาที่อนุมัติ)");
    }
}
