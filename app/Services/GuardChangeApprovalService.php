<?php

namespace App\Services;

use App\Models\GuardChangeRequest;
use App\Models\DutyRoster;
use App\Models\SeniorDutyRoster;
use App\Models\User;
use App\Notifications\GuardChangeStatusUpdated;
use App\Notifications\NewGuardChangeNotification;
use App\Services\FCMService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class GuardChangeApprovalService
{
    public function __construct()
    {
    }

    /**
     * ตำแหน่งเวรยาม mapping
     */
    protected const DUTY_POSITIONS = [
        'senior_duty_officer' => 'นายทหารเวรอาวุโส',
        'duty_officer' => 'นายทหารเวร',
        'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
    ];

    /**
     * Status labels mapping
     */
    protected const STATUS_LABELS = [
        'pending' => 'รอผู้เปลี่ยนแทนตอบรับ',
        'approved' => 'รอ รอง ผอ. อนุมัติ',
        'director_approved' => 'รอ ผอ. อนุมัติ',
        'fully_approved' => 'อนุมัติเสร็จสมบูรณ์',
        'rejected' => 'ถูกปฏิเสธ',
        'cancelled' => 'ยกเลิกแล้ว',
    ];

    /**
     * Get status label in Thai
     */
    public function getStatusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? $status;
    }

    /**
     * Check if actor can approve/reject at the current step
     */
    public function canActorApprove(GuardChangeRequest $guardChange, User $actor): bool
    {
        if ($actor->role === 'admin') return true;

        $status = $guardChange->status;

        switch ($status) {
            case 'pending':
                // Only the replacement user can approve at step 1
                return $guardChange->replacement_user_id === $actor->id;

            case 'approved':
                // Deputy Director or Director can approve at step 2
                return in_array($actor->role, ['deputy_director', 'director']);

            case 'director_approved':
                // Only Director can do final approval
                return $actor->role === 'director';

            default:
                return false;
        }
    }

    /**
     * Step 1: Replacement user approves
     */
    public function approveByReplacement(GuardChangeRequest $guardChange, User $actor, $comment = null)
    {
        if (!$this->canActorApprove($guardChange, $actor)) {
            throw new Exception("คุณไม่มีสิทธิ์อนุมัติคำขอเปลี่ยนเวรนี้ในขั้นตอนนี้");
        }

        if ($guardChange->status !== 'pending') {
            throw new Exception("สถานะคำขอไม่ถูกต้องสำหรับการอนุมัติขั้นตอนนี้");
        }

        // Handle auto-signature
        $signaturePath = $this->handleAutoSignature($guardChange, $actor, 'guard_sig');

        $guardChange->update([
            'status' => 'approved',
            'approver_id' => $actor->id,
            'approval_signature' => $signaturePath,
            'approval_comment' => $comment ?: null,
            'approved_at' => now(),
        ]);

        // Notify requester
        $requester = $guardChange->user;
        $requester->notify(new GuardChangeStatusUpdated($guardChange, 'approved', $actor));

        if ($requester->fcm_token) {
            (new FCMService())->sendNotification(
                $requester->fcm_token,
                'การขอเปลี่ยนเวรได้รับการตอบรับ ✅',
                "{$actor->rank} {$actor->name} ตอบรับคำขอเปลี่ยนเวรของคุณแล้ว",
                ['type' => 'guard_change_status', 'request_id' => $guardChange->id]
            );
        }

        // Notify Deputy Directors (next step) via FCM/DB
        $deputyDirectors = User::where('role', 'deputy_director')->get();
        foreach ($deputyDirectors as $deputy) {
            $deputy->notify(new NewGuardChangeNotification($guardChange, $requester));
            if ($deputy->fcm_token) {
                (new FCMService())->sendNotification(
                    $deputy->fcm_token,
                    'มีคำขอเปลี่ยนเวรใหม่ (รออนุมัติ) 🔔',
                    "มีคำขอเปลี่ยนเวรของ {$requester->rank} {$requester->name} รอการอนุมัติจากคุณ",
                    ['type' => 'new_guard_change_approval', 'request_id' => $guardChange->id]
                );
            }
        }

        return true;
    }

    /**
     * Step 2: Deputy Director approves
     */
    public function approveByDeputyDirector(GuardChangeRequest $guardChange, User $actor, $comment = null)
    {
        if (!$this->canActorApprove($guardChange, $actor)) {
            throw new Exception("คุณไม่มีสิทธิ์อนุมัติคำขอเปลี่ยนเวรนี้ในขั้นตอนนี้");
        }

        if ($guardChange->status !== 'approved') {
            throw new Exception("สถานะคำขอไม่ถูกต้องสำหรับการอนุมัติขั้นตอนนี้");
        }

        // If Director approves directly at deputy stage, skip to fully_approved
        if ($actor->role === 'director') {
            return $this->approveByDirector($guardChange, $actor, $comment);
        }

        $signaturePath = $this->handleAutoSignature($guardChange, $actor, 'guard_director_sig');

        $guardChange->update([
            'status' => 'director_approved',
            'director_approver_id' => $actor->id,
            'director_signature' => $signaturePath,
            'director_comment' => $comment ?: null,
            'director_approved_at' => now(),
        ]);

        // Notify requester
        $requester = $guardChange->user;
        $requester->notify(new GuardChangeStatusUpdated($guardChange, 'director_approved', $actor));

        if ($requester->fcm_token) {
            (new FCMService())->sendNotification(
                $requester->fcm_token,
                'การขอเปลี่ยนเวรผ่านการอนุมัติเบื้องต้น ℹ️',
                "รอง ผอ. {$actor->name} ได้อนุมัติคำขอเปลี่ยนเวรของคุณแล้ว (รอ ผอ. อนุมัติ)",
                ['type' => 'guard_change_status', 'request_id' => $guardChange->id]
            );
        }

        // Notify Directors (final step) via FCM/DB
        $directors = User::where('role', 'director')->get();
        foreach ($directors as $director) {
            $director->notify(new NewGuardChangeNotification($guardChange, $requester));
            if ($director->fcm_token) {
                (new FCMService())->sendNotification(
                    $director->fcm_token,
                    'มีคำขอเปลี่ยนเวรใหม่ (รออนุมัติสุดท้าย) 🔔',
                    "มีคำขอเปลี่ยนเวรของ {$requester->rank} {$requester->name} รอการอนุมัติจากคุณ",
                    ['type' => 'new_guard_change_approval', 'request_id' => $guardChange->id]
                );
            }
        }

        return true;
    }

    /**
     * Step 3: Director final approval
     */
    public function approveByDirector(GuardChangeRequest $guardChange, User $actor, $comment = null)
    {
        if ($actor->role !== 'director' && $actor->role !== 'admin') {
            throw new Exception("คุณไม่มีสิทธิ์อนุมัติคำขอเปลี่ยนเวรนี้ในขั้นตอนนี้");
        }

        if (!in_array($guardChange->status, ['director_approved', 'approved'])) {
            throw new Exception("สถานะคำขอไม่ถูกต้องสำหรับการอนุมัติขั้นตอนนี้");
        }

        $signaturePath = $this->handleAutoSignature($guardChange, $actor, 'guard_final_sig');

        $guardChange->update([
            'status' => 'fully_approved',
            'final_approver_id' => $actor->id,
            'final_signature' => $signaturePath,
            'final_comment' => $comment ?: null,
            'final_approved_at' => now(),
        ]);

        // Notify requester
        $requester = $guardChange->user;
        $requester->notify(new GuardChangeStatusUpdated($guardChange, 'fully_approved', $actor));

        if ($requester->fcm_token) {
            (new FCMService())->sendNotification(
                $requester->fcm_token,
                'การขอเปลี่ยนเวรอนุมัติเสร็จสมบูรณ์ 🎉',
                "ผอ. {$actor->name} ได้อนุมัติคำขอเปลี่ยนเวรของคุณเรียบร้อยแล้ว",
                ['type' => 'guard_change_status', 'request_id' => $guardChange->id]
            );
        }

        return true;
    }

    /**
     * Unified approve method - determines which step based on current status
     */
    public function approve(GuardChangeRequest $guardChange, User $actor, $comment = null)
    {
        $status = $guardChange->status;

        switch ($status) {
            case 'pending':
                return $this->approveByReplacement($guardChange, $actor, $comment);
            case 'approved':
                return $this->approveByDeputyDirector($guardChange, $actor, $comment);
            case 'director_approved':
                return $this->approveByDirector($guardChange, $actor, $comment);
            default:
                throw new Exception("สถานะคำขอไม่ถูกต้องสำหรับการอนุมัติ (สถานะปัจจุบัน: {$this->getStatusLabel($status)})");
        }
    }

    /**
     * Unified reject method
     */
    public function reject(GuardChangeRequest $guardChange, User $actor, $comment = null)
    {
        if (!$this->canActorApprove($guardChange, $actor)) {
            throw new Exception("คุณไม่มีสิทธิ์ปฏิเสธคำขอเปลี่ยนเวรนี้ในขั้นตอนนี้");
        }

        $previousStatus = $guardChange->status;
        $rejectComment = $comment ?: 'ไม่ได้ระบุเหตุผล';

        // Determine which field to update based on step
        $updateData = [
            'status' => 'rejected',
        ];

        if ($previousStatus === 'pending') {
            $updateData['approver_id'] = $actor->id;
            $updateData['approval_comment'] = $rejectComment;
            $updateData['approved_at'] = now();
        } elseif ($previousStatus === 'approved') {
            $updateData['director_approver_id'] = $actor->id;
            $updateData['director_comment'] = $rejectComment;
            $updateData['director_approved_at'] = now();
        } elseif ($previousStatus === 'director_approved') {
            $updateData['final_approver_id'] = $actor->id;
            $updateData['final_comment'] = $rejectComment;
            $updateData['final_approved_at'] = now();
        }

        $guardChange->update($updateData);

        // Notify requester
        $requester = $guardChange->user;
        $requester->notify(new GuardChangeStatusUpdated($guardChange, 'rejected', $actor));

        if ($requester->fcm_token) {
            (new FCMService())->sendNotification(
                $requester->fcm_token,
                'การขอเปลี่ยนเวรถูกปฏิเสธ ❌',
                "{$actor->rank} {$actor->name} ปฏิเสธคำขอเปลี่ยนเวรของคุณ",
                ['type' => 'guard_change_status', 'request_id' => $guardChange->id]
            );
        }

        return true;
    }

    /**
     * Handle auto-signature
     */
    protected function handleAutoSignature(GuardChangeRequest $guardChange, User $actor, $prefix = 'guard_sig')
    {
        if ($actor->signature && Storage::disk('public')->exists($actor->signature)) {
            $extension = pathinfo($actor->signature, PATHINFO_EXTENSION);
            $fileName = "signatures/{$prefix}_" . time() . '_' . $guardChange->id . '_' . $actor->id . '.' . $extension;
            Storage::disk('public')->copy($actor->signature, $fileName);
            return $fileName;
        }

        return null;
    }

    /**
     * อัปเดตตารางเวร (Duty Roster) ทันทีเมื่อส่งคำขอเปลี่ยนเวร
     * สลับผู้เข้าเวรเดิมเป็นผู้เข้าเวรแทน
     */
    public function updateDutyRosterOnRequest(GuardChangeRequest $guardChange)
    {
        $originalUserId = $guardChange->user_id;
        $replacementUserId = $guardChange->replacement_user_id;
        $dutyPosition = $guardChange->duty_position;

        // กรณีนายทหารเวรอาวุโส → อัปเดต senior_duty_rosters
        if ($dutyPosition === 'senior_duty_officer') {
            $seniorRoster = SeniorDutyRoster::where('senior_officer_id', $originalUserId)
                ->where('start_date', '<=', $guardChange->duty_date)
                ->where('end_date', '>=', $guardChange->duty_date)
                ->first();

            if ($seniorRoster) {
                $seniorRoster->update(['senior_officer_id' => $replacementUserId]);
            }
            return;
        }

        // กรณีนายทหารเวร / ผู้ช่วยนายทหารเวร → อัปเดต duty_rosters
        $roster = DutyRoster::where('duty_date', $guardChange->duty_date)->first();

        if (!$roster) {
            return;
        }

        if ($dutyPosition === 'duty_officer') {
            if ($roster->duty_officer_id == $originalUserId) {
                $roster->update(['duty_officer_id' => $replacementUserId]);
            }
        }

        if ($dutyPosition === 'assistant_duty_officer') {
            if ($roster->assistant_duty_officer_id == $originalUserId) {
                $roster->update(['assistant_duty_officer_id' => $replacementUserId]);
            }
        }

        Log::info("Duty Roster updated on guard change request", [
            'guard_change_id' => $guardChange->id,
            'duty_date' => $guardChange->duty_date->format('Y-m-d'),
            'original_user' => $originalUserId,
            'replacement_user' => $replacementUserId,
            'position' => $dutyPosition,
        ]);
    }
}
