<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\LeaveApproval;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Events\LeaveRequestStatusChanged;
use Illuminate\Support\Facades\Storage;
use Exception;

class LeaveApprovalService
{
    /**
     * Unified Approval Logic
     */
    public function approve(LeaveRequest $leaveRequest, User $actor, $comment = null, $signaturePath = null)
    {
        $requester = $leaveRequest->user;
        $leaveRequest->load('leaveType');
        $leaveSlug = strtolower($leaveRequest->leaveType->slug ?? '');
        
        $isVacation = $leaveSlug === 'vacation' || str_contains($leaveRequest->leaveType->name ?? '', 'พักผ่อน');
        $isSickOrPersonal = in_array($leaveSlug, ['sick', 'personal']);
        $isTemporary = $leaveSlug === 'temporary';

        // 0. Authorization Checks
        if (!$this->canActorApprove($leaveRequest, $actor)) {
            throw new Exception("คุณไม่มีสิทธิ์อนุมัติใบลาของ {$requester->name} ในขั้นตอนนี้");
        }

        // 1. Determine which step we are at
        $status = $leaveRequest->status;
        $nextStatus = null;
        $stepName = '';

        if ($status === 'pending_supervisor') {
            $stepName = 'supervisor';
            if ($isTemporary) {
                $nextStatus = 'approved';
            } else {
                // Check if student course
                $studentCourses = ['หลักสูตรนายทหารพลาธิการชั้นนายเรือ ประจำปีงบประมาณ 69', 'หลักสูตรอาชีพเพื่อเลื่อนฐานะชั้น จ.อ.'];
                $isStudent = in_array($requester->department, $studentCourses);
                
                if ($isStudent && $isSickOrPersonal && $requester->manager_id) {
                    $nextStatus = 'pending_manager';
                } else {
                    $nextStatus = 'pending_deputy_director';
                }
            }
        } elseif ($status === 'pending_manager') {
            $stepName = 'manager';
            $nextStatus = 'approved';
        } elseif ($status === 'pending_deputy_director') {
            $stepName = 'deputy_director';
            // If Director approves directly at this stage (not likely via LINE but good for parity)
            if ($actor->role === 'director') {
                $nextStatus = 'approved';
                $stepName = 'director';
            } else {
                $nextStatus = 'pending_director';
            }
        } elseif ($status === 'pending_director') {
            $stepName = 'director';
            $nextStatus = 'approved';
        }

        if (!$nextStatus) {
            throw new Exception('Invalid leave request status for approval.');
        }

        // 2. Handle Signature if not provided but saved exists
        if (!$signaturePath && $actor->signature && in_array($stepName, ['supervisor', 'manager', 'director'])) {
            $extension = pathinfo($actor->signature, PATHINFO_EXTENSION);
            $fileName = 'signatures/sig_auto_' . time() . '_' . $leaveRequest->id . '_' . $actor->id . '.' . $extension;
            if (Storage::disk('public')->exists($actor->signature)) {
                Storage::disk('public')->copy($actor->signature, $fileName);
                $signaturePath = $fileName;
            }
        }

        // 3. Update Request
        $leaveRequest->status = $nextStatus;
        $leaveRequest->save();

        // 4. Log Approval
        $action = ($stepName === 'director' && !$isVacation) ? 'acknowledged' : 
                  (($stepName === 'deputy_director') ? 'acknowledged' : 'approved');

        $leaveRequest->approvals()->create([
            'approver_id' => $actor->id,
            'step' => $stepName,
            'action' => $action,
            'comment' => $comment ?: ($actor->line_user_id ? 'อนุมัติผ่าน LINE' : null),
            'signature' => $signaturePath,
            'ip_address' => request()->ip() ?: 'LINE_API'
        ]);

        // 5. Deduct Balance if fully approved
        if ($nextStatus === 'approved' && !$isTemporary) {
            $this->deductBalance($leaveRequest);
        }

        // 6. Trigger Event
        event(new LeaveRequestStatusChanged($leaveRequest, $nextStatus, $actor));

        return true;
    }

    /**
     * Unified Rejection Logic
     */
    public function reject(LeaveRequest $leaveRequest, User $actor, $comment)
    {
        if (!$this->canActorApprove($leaveRequest, $actor)) {
            throw new Exception("คุณไม่มีสิทธิ์ดำเนินการกับใบลานี้");
        }

        $step = $leaveRequest->status;
        $leaveRequest->status = 'rejected';
        $leaveRequest->save();

        $leaveRequest->approvals()->create([
            'approver_id' => $actor->id,
            'step' => $step,
            'action' => 'rejected',
            'comment' => $comment ?: ($actor->line_user_id ? 'ปฏิเสธผ่าน LINE' : 'ไม่ได้ระบุเหตุผล'),
            'ip_address' => request()->ip() ?: 'LINE_API'
        ]);

        event(new LeaveRequestStatusChanged($leaveRequest, 'rejected', $actor));

        return true;
    }

    /**
     * Check if Actor has Permission for the CURRENT step of the request
     */
    public function canActorApprove(LeaveRequest $leaveRequest, User $actor): bool
    {
        if ($actor->role === 'admin') return true;

        $status = $leaveRequest->status;
        $requester = $leaveRequest->user;

        switch ($status) {
            case 'pending_supervisor':
                return $requester->supervisor_id === $actor->id;
            case 'pending_manager':
                return $requester->manager_id === $actor->id;
            case 'pending_deputy_director':
                return $actor->role === 'deputy_director' || $actor->role === 'director';
            case 'pending_director':
                return $actor->role === 'director';
            default:
                return false;
        }
    }

    protected function deductBalance(LeaveRequest $leaveRequest)
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
}
