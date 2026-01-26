<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GuardChangeStatusUpdated extends Notification
{
    use Queueable;

    public $guardChangeRequest;
    public $status;
    public $approver;

    /**
     * Create a new notification instance.
     */
    public function __construct($guardChangeRequest, $status, $approver)
    {
        $this->guardChangeRequest = $guardChangeRequest;
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
        $statusText = '';
        switch ($this->status) {
            case 'approved':
                $statusText = 'ได้รับการตอบรับจากผู้เปลี่ยนแทน';
                break;
            case 'rejected':
                $statusText = 'ถูกปฏิเสธ';
                break;
            case 'director_approved':
                $statusText = 'ผ่านการอนุมัติจาก รอง ผอ.';
                break;
            case 'fully_approved':
                $statusText = 'อนุมัติเรียบร้อยแล้ว (เสร็จสมบูรณ์)';
                break;
        }

        $message = "คำขอเปลี่ยนเวรของคุณวันที่ {$this->guardChangeRequest->duty_date->format('d/m/Y')} {$statusText} โดย {$this->approver->rank} {$this->approver->name}";

        return [
            'guard_change_request_id' => $this->guardChangeRequest->id,
            'status' => $this->status,
            'approver_name' => $this->approver->name,
            'message' => $message,
            'duty_date' => $this->guardChangeRequest->duty_date->format('d/m/Y'),
        ];
    }
}
