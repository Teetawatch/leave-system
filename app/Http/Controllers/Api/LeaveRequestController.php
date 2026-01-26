<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveRequestResource;
use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Notifications\NewLeaveRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class LeaveRequestController extends Controller
{
    /**
     * Get leave requests for authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = LeaveRequest::with(['leaveType', 'approvals.approver'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Optional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        $requests = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => LeaveRequestResource::collection($requests),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ],
        ]);
    }

    /**
     * Get a single leave request
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $leaveRequest = LeaveRequest::with(['user', 'leaveType', 'approvals.approver'])
            ->findOrFail($id);

        // Check authorization - user can view their own or if they are approver
        if ($leaveRequest->user_id !== $user->id && !$this->canViewRequest($user, $leaveRequest)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์ดูข้อมูลนี้',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new LeaveRequestResource($leaveRequest),
        ]);
    }

    /**
     * Create a new leave request
     */
    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'contact_address' => 'nullable|array',
        ]);

        $user = $request->user();
        $leaveType = LeaveType::findOrFail($request->leave_type_id);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $diffDays = $startDate->diffInDays($endDate) + 1;

        // Business Rules Validation

        // 1. Advance Notice Check
        if ($leaveType->requires_advance_notice && $leaveType->slug !== 'personal') {
            $daysInAdvance = now()->diffInDays($startDate, false);
            if ($daysInAdvance < $leaveType->advance_notice_days) {
                return response()->json([
                    'success' => false,
                    'message' => "ประเภทการลานี้ต้องยื่นล่วงหน้าอย่างน้อย {$leaveType->advance_notice_days} วัน",
                ], 422);
            }
        }

        // 2. Retroactive Check
        if (!$leaveType->allows_retroactive) {
            if ($startDate->isPast() && !$startDate->isToday()) {
                return response()->json([
                    'success' => false,
                    'message' => "ประเภทการลานี้ไม่สามารถยื่นย้อนหลังได้",
                ], 422);
            }
        } else {
            $daysPast = $startDate->diffInDays(now(), false);
            if ($daysPast > 7) {
                return response()->json([
                    'success' => false,
                    'message' => "ไม่สามารถยื่นย้อนหลังเกิน 7 วันได้",
                ], 422);
            }
        }

        // 3. Check Balance
        $currentYear = now()->year;
        $balance = LeaveBalance::firstOrCreate(
            ['user_id' => $user->id, 'leave_type_id' => $leaveType->id, 'year' => $currentYear],
            [
                'total_days' => $leaveType->max_days_per_year,
                'used_days' => 0,
                'remaining_days' => $leaveType->max_days_per_year
            ]
        );

        if ($balance->remaining_days < $diffDays) {
            return response()->json([
                'success' => false,
                'message' => "วันลาคงเหลือไม่เพียงพอ (เหลือ {$balance->remaining_days} วัน, ต้องการ {$diffDays} วัน)",
            ], 422);
        }

        // Create Leave Request
        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $diffDays,
            'reason' => $request->reason,
            'contact_address' => $request->contact_address,
            'status' => 'pending_supervisor',
        ]);

        $leaveRequest->load(['leaveType', 'user']);

        // Notify supervisor
        Log::info('Checking supervisor for user: ' . $user->id . ' Supervisor ID: ' . $user->supervisor_id);

        if ($user->supervisor_id) {
            $supervisor = User::find($user->supervisor_id);

            // 1. Send Database/Email Notification (Existing)
            if ($supervisor) {
                Log::info('Supervisor found: ' . $supervisor->id . ' Has Token: ' . ($supervisor->fcm_token ? 'YES' : 'NO'));
                $supervisor->notify(new NewLeaveRequestNotification($leaveRequest, $user));

                // 2. Send Push Notification via FCM (New)
                if ($supervisor->fcm_token) {
                    $fcmService = new \App\Services\FCMService();
                    $fcmService->sendNotification(
                        $supervisor->fcm_token,
                        'มีใบลาเข้ามาใหม่ 🔔',
                        "{$user->name} ขอลา {$leaveType->name} ({$diffDays} วัน)",
                        ['request_id' => $leaveRequest->id]
                    );
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'ส่งคำขอลาเรียบร้อยแล้ว',
            'data' => new LeaveRequestResource($leaveRequest->load(['leaveType', 'approvals.approver'])),
        ], 201);
    }

    /**
     * Cancel a leave request
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();

        $leaveRequest = LeaveRequest::findOrFail($id);

        // Check ownership
        if ($leaveRequest->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์ยกเลิกคำขอนี้',
            ], 403);
        }

        // Check status
        if (!in_array($leaveRequest->status, ['pending_supervisor', 'pending_head', 'pending_manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถยกเลิกคำขอที่ดำเนินการเสร็จสิ้นแล้วได้',
            ], 422);
        }

        $leaveRequest->status = 'cancelled';
        $leaveRequest->cancelled_at = now();
        $leaveRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'ยกเลิกคำขอเรียบร้อยแล้ว',
            'data' => new LeaveRequestResource($leaveRequest->load(['leaveType', 'approvals.approver'])),
        ]);
    }

    /**
     * Check if user can view a leave request
     */
    private function canViewRequest($user, $leaveRequest)
    {
        // Admins and directors can view all
        if (in_array($user->role, ['admin', 'director', 'deputy_director'])) {
            return true;
        }

        // Department heads can view requests from their department
        if ($user->role === 'department_head') {
            $requestUser = $leaveRequest->user;
            return $requestUser && $requestUser->department === $user->department;
        }

        // Supervisors can view their subordinates' requests
        $requestUser = $leaveRequest->user;
        if ($requestUser && $requestUser->supervisor_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Export leave request to PDF
     */
    public function exportPdf(Request $request, $id)
    {
        $user = $request->user();
        $leaveRequest = LeaveRequest::with(['user', 'leaveType', 'approvals.approver'])
            ->findOrFail($id);

        // Check authorization - user can view their own or if they are authorized roles
        if ($leaveRequest->user_id !== $user->id && !$this->canViewRequest($user, $leaveRequest)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์ดูข้อมูลนี้',
            ], 403);
        }

        $leaveBalance = LeaveBalance::where('user_id', $leaveRequest->user_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', now()->year)
            ->first();

        // Create default balance if not exists
        if (!$leaveBalance) {
            $leaveBalance = new LeaveBalance([
                'user_id' => $leaveRequest->user_id,
                'leave_type_id' => $leaveRequest->leave_type_id,
                'year' => now()->year,
                'total_days' => 10,
                'used_days' => 0,
                'remaining_days' => 10,
            ]);
        }

        // Previous year balance (optional)
        $lastYearBalance = LeaveBalance::where('user_id', $leaveRequest->user_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', now()->year - 1)
            ->first();

        // Determine View based on Leave Type
        $viewName = 'leave_request.pdf'; // Default

        if ($leaveRequest->leaveType) {
            $slug = $leaveRequest->leaveType->slug;
            if ($slug == 'sick') {
                $viewName = 'leave_request.pdf_sick';
            } elseif ($slug == 'personal') {
                $viewName = 'leave_request.pdf_personal';
            }
        }

        $pdf = Pdf::loadView($viewName, compact('leaveRequest', 'leaveBalance', 'lastYearBalance'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('leave-request-' . $leaveRequest->id . '.pdf');
    }
}
