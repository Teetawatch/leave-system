<?php

namespace App\Listeners;

use App\Events\LeaveRequestStatusChanged;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;

class SendTelegramLeaveStatusChanged
{
    public function handle(LeaveRequestStatusChanged $event): void
    {
        try {
            $leaveRequest = $event->leaveRequest;
            $status = $event->status;
            $actor = $event->actor;

            $leaveRequest->load(['user', 'leaveType']);
            $requester = $leaveRequest->user;
            $telegram = new TelegramService();

            // 1. Notify the requester about the status change
            if (!empty($requester->telegram_chat_id)) {
                $telegram->notifyLeaveStatusChanged($leaveRequest, $status, $actor, $requester->telegram_chat_id);
            }

            // 2. If forwarded to next approver, notify them with approve/reject buttons
            if (!in_array($status, ['approved', 'rejected', 'cancelled'])) {
                $nextApprovers = $this->getApproversForStatus($status, $requester);
                foreach ($nextApprovers as $approver) {
                    if (!empty($approver->telegram_chat_id)) {
                        $telegram->notifyNewLeaveRequest($leaveRequest, $approver->telegram_chat_id);
                    }
                }
            }

            Log::info('[Telegram] LeaveStatusChanged notification sent', [
                'leave_request_id' => $leaveRequest->id,
                'new_status' => $status,
            ]);
        } catch (\Throwable $e) {
            Log::error('[Telegram] LeaveStatusChanged error: ' . $e->getMessage());
        }
    }

    protected function getApproversForStatus(string $status, User $requester)
    {
        return match ($status) {
            'pending_supervisor', 'pending_head' => User::where(function ($q) use ($requester) {
                $q->where('id', $requester->supervisor_id);
                if ($requester->department) {
                    $q->orWhere(function ($qq) use ($requester) {
                        $qq->where('role', 'department_head')
                           ->where('department', $requester->department);
                    });
                }
            })->whereNotNull('telegram_chat_id')->get(),

            'pending_manager' => User::where('id', $requester->manager_id)
                ->whereNotNull('telegram_chat_id')->get(),

            'pending_deputy_director' => User::whereIn('role', ['deputy_director', 'director'])
                ->whereNotNull('telegram_chat_id')->get(),

            'pending_director' => User::where('role', 'director')
                ->whereNotNull('telegram_chat_id')->get(),

            default => collect(),
        };
    }
}
