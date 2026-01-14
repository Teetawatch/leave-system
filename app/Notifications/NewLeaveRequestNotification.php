<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLeaveRequestNotification extends Notification
{
    use Queueable;

    public $leaveRequest;
    public $requester;

    /**
     * Create a new notification instance.
     */
    public function __construct($leaveRequest, $requester)
    {
        $this->leaveRequest = $leaveRequest;
        $this->requester = $requester;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $requesterName = $this->requester->rank . ' ' . $this->requester->name;
        $leaveTypeName = $this->leaveRequest->leaveType->name;
        $totalDays = $this->leaveRequest->total_days;
        
        $message = "{$requesterName} ยื่นคำขอ{$leaveTypeName} จำนวน {$totalDays} วัน รอการอนุมัติจากคุณ";

        return [
            'leave_request_id' => $this->leaveRequest->id,
            'status' => 'pending',
            'requester_name' => $requesterName,
            'requester_id' => $this->requester->id,
            'message' => $message,
            'leave_type' => $leaveTypeName,
            'total_days' => $totalDays,
        ];
    }
}
