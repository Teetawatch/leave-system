<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveStatusUpdated extends Notification
{
    use Queueable;

    public $leaveRequest;
    public $status;
    public $approver;

    /**
     * Create a new notification instance.
     */
    public function __construct($leaveRequest, $status, $approver)
    {
        $this->leaveRequest = $leaveRequest;
        $this->status = $status;
        $this->approver = $approver;
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
        $message = "คำขอลา \"{$this->leaveRequest->leaveType->name}\" ของคุณได้รับการ" .
            ($this->status === 'approved' ? 'อนุมัติ' : 'ปฏิเสธ') .
            " โดย {$this->approver->name}";

        return [
            'leave_request_id' => $this->leaveRequest->id,
            'status' => $this->status,
            'approver_name' => $this->approver->name,
            'title' => 'อัปเดตสถานะการลา',
            'message' => $message,
            'leave_type' => $this->leaveRequest->leaveType->name,
            'type' => 'leave',
        ];
    }
}
