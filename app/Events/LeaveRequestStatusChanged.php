<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leaveRequest;
    public $status;
    public $actor;

    /**
     * Create a new event instance.
     */
    public function __construct($leaveRequest, $status, $actor)
    {
        $this->leaveRequest = $leaveRequest;
        $this->status = $status;
        $this->actor = $actor;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
