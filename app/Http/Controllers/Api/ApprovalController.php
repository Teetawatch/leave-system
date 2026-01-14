<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveRequestResource;
use App\Models\LeaveRequest;
use App\Models\LeaveApproval;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Notifications\LeaveStatusUpdated;
use App\Notifications\NewLeaveRequestNotification;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    /**
     * Get pending approvals for authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = LeaveRequest::with(['user', 'leaveType', 'approvals.approver']);

        // Filter based on user role and approval stage
        $query->where(function($q) use ($user) {
            // Case 1: Pending Supervisor - User is the supervisor
            $q->where(function($subQ) use ($user) {
                $subQ->where('status', 'pending_supervisor')
                     ->whereHas('user', function($userQ) use ($user) {
                         $userQ->where('supervisor_id', $user->id);
                     });
            });
            
            // Case 2: Pending Manager - User is the manager
            $q->orWhere(function($subQ) use ($user) {
                $subQ->where('status', 'pending_manager')
                     ->whereHas('user', function($userQ) use ($user) {
                         $userQ->where('manager_id', $user->id);
                     });
            });

            // Case 3: Admin/Director can see all pending
            if (in_array($user->role, ['admin', 'director', 'deputy_director'])) {
                $q->orWhereIn('status', ['pending_supervisor', 'pending_manager']);
            }
        });

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

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
     * Approve a leave request
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'comment' => 'nullable|string|max:500',
            'signature' => 'nullable|string', // Base64 signature
        ]);

        $user = $request->user();
        $leaveRequest = LeaveRequest::with(['user', 'leaveType'])->findOrFail($id);
        $requester = $leaveRequest->user;

        // Handle Signature
        $signaturePath = $this->handleSignature($request, $leaveRequest, $user);

        // Process based on current status
        if ($leaveRequest->status === 'pending_supervisor') {
            return $this->handleSupervisorApproval($request, $leaveRequest, $user, $requester, $signaturePath);
        }

        if ($leaveRequest->status === 'pending_manager') {
            return $this->handleManagerApproval($request, $leaveRequest, $user, $requester, $signaturePath);
        }

        return response()->json([
            'success' => false,
            'message' => 'สถานะใบลาไม่ถูกต้องสำหรับการอนุมัติ',
        ], 422);
    }

    /**
     * Reject a leave request
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'comment' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $leaveRequest = LeaveRequest::with(['user', 'leaveType'])->findOrFail($id);

        // Check authorization
        if (!$this->canApprove($user, $leaveRequest)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์ปฏิเสธคำขอนี้',
            ], 403);
        }

        // Log Rejection
        LeaveApproval::create([
            'leave_request_id' => $leaveRequest->id,
            'approver_id' => $user->id,
            'step' => $leaveRequest->status,
            'action' => 'rejected',
            'comment' => $request->comment,
            'ip_address' => $request->ip(),
        ]);

        $leaveRequest->status = 'rejected';
        $leaveRequest->save();

        // Notify User
        $leaveRequest->user->notify(new LeaveStatusUpdated($leaveRequest, 'rejected', $user));

        return response()->json([
            'success' => true,
            'message' => 'ปฏิเสธคำขอเรียบร้อยแล้ว',
            'data' => new LeaveRequestResource($leaveRequest->load(['leaveType', 'approvals.approver'])),
        ]);
    }

    /**
     * Handle signature upload/copy
     */
    private function handleSignature(Request $request, LeaveRequest $leaveRequest, $user)
    {
        if ($request->filled('signature')) {
            $imageData = $request->input('signature');
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
            $imageData = base64_decode($imageData);
            
            $fileName = 'signatures/sig_' . time() . '_' . $leaveRequest->id . '_' . $user->id . '.png';
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageData);
            return $fileName;
        }
        
        if ($request->input('use_saved_signature') && $user->signature) {
            $extension = pathinfo($user->signature, PATHINFO_EXTENSION);
            $fileName = 'signatures/sig_' . time() . '_' . $leaveRequest->id . '_' . $user->id . '.' . $extension;
            
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->signature)) {
                \Illuminate\Support\Facades\Storage::disk('public')->copy($user->signature, $fileName);
                return $fileName;
            }
        }

        return null;
    }

    /**
     * Handle supervisor approval (Step 1)
     */
    private function handleSupervisorApproval(Request $request, LeaveRequest $leaveRequest, $user, $requester, $signaturePath)
    {
        // Check authorization
        if ($requester->supervisor_id !== $user->id && !in_array($user->role, ['admin', 'director', 'deputy_director'])) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์อนุมัติคำขอนี้',
            ], 403);
        }

        $leaveSlug = strtolower($leaveRequest->leaveType->slug ?? '');
        $leaveName = $leaveRequest->leaveType->name ?? '';
        $isVacation = $leaveSlug === 'vacation' || $leaveSlug === 'annual' || str_contains($leaveName, 'พักผ่อน');
        $hasManager = !empty($requester->manager_id);
        $isSickOrPersonal = in_array($leaveSlug, ['sick', 'personal']);

        if (($isVacation || $isSickOrPersonal) && $hasManager) {
            // Move to Step 2
            $leaveRequest->status = 'pending_manager';
            $leaveRequest->save();

            $leaveRequest->approvals()->create([
                'approver_id' => $user->id,
                'step' => 'supervisor',
                'action' => 'approved',
                'comment' => $request->comment,
                'signature' => $signaturePath,
                'ip_address' => $request->ip(),
            ]);

            // Notify Manager
            $manager = User::find($requester->manager_id);
            if ($manager) {
                $manager->notify(new NewLeaveRequestNotification($leaveRequest, $requester));
            }

            return response()->json([
                'success' => true,
                'message' => 'อนุมัติขั้นที่ 1 เรียบร้อยแล้ว รอผู้บังคับบัญชาดำเนินการขั้นถัดไป',
                'data' => new LeaveRequestResource($leaveRequest->load(['leaveType', 'approvals.approver'])),
            ]);
        } else {
            // Final Approval
            $leaveRequest->status = 'approved';
            $leaveRequest->save();

            LeaveApproval::create([
                'leave_request_id' => $leaveRequest->id,
                'approver_id' => $user->id,
                'step' => 'supervisor',
                'action' => 'approved',
                'comment' => $request->comment,
                'signature' => $signaturePath,
                'ip_address' => $request->ip(),
            ]);

            $this->deductBalance($leaveRequest);
            $requester->notify(new LeaveStatusUpdated($leaveRequest, 'approved', $user));

            return response()->json([
                'success' => true,
                'message' => 'อนุมัติการลาเรียบร้อยแล้ว',
                'data' => new LeaveRequestResource($leaveRequest->load(['leaveType', 'approvals.approver'])),
            ]);
        }
    }

    /**
     * Handle manager approval (Step 2)
     */
    private function handleManagerApproval(Request $request, LeaveRequest $leaveRequest, $user, $requester, $signaturePath)
    {
        // Check authorization
        if ($requester->manager_id !== $user->id && !in_array($user->role, ['admin', 'director', 'deputy_director'])) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์อนุมัติคำขอนี้',
            ], 403);
        }

        // Final Approval
        $leaveRequest->status = 'approved';
        $leaveRequest->save();

        LeaveApproval::create([
            'leave_request_id' => $leaveRequest->id,
            'approver_id' => $user->id,
            'step' => 'manager',
            'action' => 'approved',
            'comment' => $request->comment,
            'signature' => $signaturePath,
            'ip_address' => $request->ip(),
        ]);

        $this->deductBalance($leaveRequest);
        $requester->notify(new LeaveStatusUpdated($leaveRequest, 'approved', $user));

        return response()->json([
            'success' => true,
            'message' => 'อนุมัติการลาขั้นสุดท้ายเรียบร้อยแล้ว',
            'data' => new LeaveRequestResource($leaveRequest->load(['leaveType', 'approvals.approver'])),
        ]);
    }

    /**
     * Deduct leave balance after approval
     */
    private function deductBalance(LeaveRequest $leaveRequest)
    {
        $balance = LeaveBalance::where('user_id', $leaveRequest->user_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', now()->year)
            ->first();

        if ($balance) {
            $balance->used_days += $leaveRequest->total_days;
            $balance->remaining_days -= $leaveRequest->total_days;
            $balance->save();
        }
    }

    /**
     * Check if user can approve the request
     */
    private function canApprove($user, LeaveRequest $leaveRequest)
    {
        $requester = $leaveRequest->user;

        // Admin/Director can approve all
        if (in_array($user->role, ['admin', 'director', 'deputy_director'])) {
            return true;
        }

        // Supervisor approval
        if ($leaveRequest->status === 'pending_supervisor' && $requester->supervisor_id === $user->id) {
            return true;
        }

        // Manager approval
        if ($leaveRequest->status === 'pending_manager' && $requester->manager_id === $user->id) {
            return true;
        }

        return false;
    }
}
