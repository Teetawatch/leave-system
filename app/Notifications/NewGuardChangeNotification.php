<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewGuardChangeNotification extends Notification
{
    use Queueable;

    public $guardChangeRequest;
    public $requester;

    /**
     * Create a new notification instance.
     */
    public function __construct($guardChangeRequest, $requester)
    {
        $this->guardChangeRequest = $guardChangeRequest;
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
        $dutyDate = $this->guardChangeRequest->duty_date->format('d/m/Y');

        $message = "{$requesterName} ขอเปลี่ยนเวรกับคุณในวันที่ {$dutyDate}";

        return [
            'guard_change_request_id' => $this->guardChangeRequest->id,
            'status' => 'pending',
            'requester_name' => $requesterName,
            'requester_id' => $this->requester->id,
            'title' => 'คำขอเปลี่ยนเวรใหม่',
            'message' => $message,
            'duty_date' => $dutyDate,
            'type' => 'guard_change',
        ];
    }
}
