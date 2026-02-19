<?php

namespace App\Listeners;

use App\Events\LeaveRequestSubmitted;
use App\Events\LeaveRequestStatusChanged;
use App\Notifications\NewLeaveRequestNotification;
use App\Notifications\LeaveStatusUpdated;
use App\Services\FCMService;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLegacyLeaveNotifications implements ShouldQueue
{
    /**
     * Handle LeaveRequestSubmitted event
     */
    public function handleSubmitted(LeaveRequestSubmitted $event)
    {
        $leaveRequest = $event->leaveRequest;
        $user = $leaveRequest->user;
        $leaveType = $leaveRequest->leaveType;
        $diffDays = $leaveRequest->total_days;

        if ($user->supervisor_id) {
            $supervisor = User::find($user->supervisor_id);
            if ($supervisor) {
                // Database Notification
                $supervisor->notify(new NewLeaveRequestNotification($leaveRequest, $user));

                // FCM Notification
                if ($supervisor->fcm_token) {
                    (new FCMService())->sendNotification(
                        $supervisor->fcm_token,
                        'มีใบลาเข้ามาใหม่ 🔔',
                        "{$user->rank} {$user->name} ขอ{$leaveType->name} จำนวน {$diffDays} วัน รอการอนุมัติจากคุณ",
                        ['type' => 'new_leave_request', 'request_id' => $leaveRequest->id]
                    );
                }
            }
        }
    }

    /**
     * Handle LeaveRequestStatusChanged event
     */
    public function handleStatusChanged(LeaveRequestStatusChanged $event)
    {
        $leaveRequest = $event->leaveRequest;
        $status = $event->status;
        $actor = $event->actor;
        $requester = $leaveRequest->user;

        // 1. Notify Requester about Final status or Rejection
        if ($status === 'approved' || $status === 'rejected') {
            $requester->notify(new LeaveStatusUpdated($leaveRequest, $status, $actor));

            if ($requester->fcm_token) {
                $title = $status === 'approved' ? 'ใบลาของคุณได้รับการอนุมัติ 🎉' : 'ใบลาของคุณถูกปฏิเสธ ❌';
                $body = $status === 'approved' 
                    ? "ใบลา{$leaveRequest->leaveType->name} ของคุณได้รับการอนุมัติเรียบร้อยแล้ว"
                    : "ใบลา{$leaveRequest->leaveType->name} ของคุณถูกปฏิเสธ";
                
                (new FCMService())->sendNotification(
                    $requester->fcm_token,
                    $title,
                    $body,
                    ['type' => 'leave_status', 'request_id' => $leaveRequest->id]
                );
            }
        }

        // 2. Notify Next Approver for intermediate steps
        if (str_starts_with($status, 'pending_')) {
            $this->notifyNextApproverLegacy($leaveRequest, $status, $requester);
        }
    }

    private function notifyNextApproverLegacy($leaveRequest, $status, $requester)
    {
        if ($status === 'pending_manager' && $requester->manager_id) {
            $manager = User::find($requester->manager_id);
            if ($manager) {
                $manager->notify(new NewLeaveRequestNotification($leaveRequest, $requester));
                if ($manager->fcm_token) {
                    (new FCMService())->sendNotification(
                        $manager->fcm_token,
                        'มีใบลาใหม่ (รออนุมัติ) 🔔',
                        "ใบลาของ {$requester->rank} {$requester->name} รอการอนุมัติจากคุณ",
                        ['type' => 'new_leave_request', 'request_id' => $leaveRequest->id]
                    );
                }
            }
        } elseif ($status === 'pending_deputy_director') {
            $deputies = User::where('role', 'deputy_director')->get();
            foreach ($deputies as $deputy) {
                $deputy->notify(new NewLeaveRequestNotification($leaveRequest, $requester));
                if ($deputy->fcm_token) {
                    (new FCMService())->sendNotification(
                        $deputy->fcm_token,
                        'มีใบลาใหม่ (รอรับทราบ) 🔔',
                        "ใบลาของ {$requester->rank} {$requester->name} รอการรับทราบจากคุณ",
                        ['type' => 'new_leave_request', 'request_id' => $leaveRequest->id]
                    );
                }
            }
        } elseif ($status === 'pending_director') {
            $directors = User::where('role', 'director')->get();
            foreach ($directors as $director) {
                $director->notify(new NewLeaveRequestNotification($leaveRequest, $requester));
                if ($director->fcm_token) {
                    (new FCMService())->sendNotification(
                        $director->fcm_token,
                        'มีใบลาใหม่ (รออนุมัติสุดท้าย) 🔔',
                        "ใบลาของ {$requester->rank} {$requester->name} รอการอนุมัติสุดท้ายจากคุณ",
                        ['type' => 'new_leave_request', 'request_id' => $leaveRequest->id]
                    );
                }
            }
        }
    }

    /**
     * Map events to methods (Since we are using one listener for multiple events)
     */
    public function subscribe($events)
    {
        $events->listen(
            LeaveRequestSubmitted::class,
            [SendLegacyLeaveNotifications::class, 'handleSubmitted']
        );

        $events->listen(
            LeaveRequestStatusChanged::class,
            [SendLegacyLeaveNotifications::class, 'handleStatusChanged']
        );
    }
}
