<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected string $apiBase = 'https://api.telegram.org/bot';

    public function __construct()
    {
        $this->botToken = (string) config('services.telegram.bot_token', '');
    }

    /**
     * Send a text message to a Telegram chat.
     */
    public function sendMessage(string $chatId, string $text, ?array $inlineKeyboard = null, string $parseMode = 'HTML'): bool
    {
        if (empty($this->botToken)) {
            Log::warning('[Telegram] Bot token not configured');
            return false;
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode,
        ];

        if ($inlineKeyboard) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => $inlineKeyboard,
            ]);
        }

        return $this->post('sendMessage', $payload);
    }

    /**
     * Answer a callback query (acknowledge inline button press).
     */
    public function answerCallbackQuery(string $callbackQueryId, string $text = '', bool $showAlert = false): bool
    {
        return $this->post('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert,
        ]);
    }

    /**
     * Edit an existing message's text.
     */
    public function editMessageText(string $chatId, int $messageId, string $text, string $parseMode = 'HTML'): bool
    {
        return $this->post('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => $parseMode,
        ]);
    }

    /**
     * Set webhook URL for the bot.
     */
    public function setWebhook(string $url, string $secretToken = ''): bool
    {
        $payload = ['url' => $url];
        if ($secretToken) {
            $payload['secret_token'] = $secretToken;
        }

        return $this->post('setWebhook', $payload);
    }

    /**
     * Remove webhook.
     */
    public function deleteWebhook(): bool
    {
        return $this->post('deleteWebhook', ['drop_pending_updates' => true]);
    }

    /**
     * Get current webhook info (for debugging).
     */
    public function getWebhookInfo(): ?array
    {
        try {
            $response = Http::timeout(10)->get($this->apiBase . $this->botToken . '/getWebhookInfo');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('[Telegram] getWebhookInfo error: ' . $e->getMessage());
            return null;
        }
    }

    // =========================================================================
    // Notification Builders
    // =========================================================================

    /**
     * Notify approvers that a new leave request has been submitted.
     * Includes inline keyboard for Approve/Reject.
     */
    public function notifyNewLeaveRequest($leaveRequest, $approverChatId): bool
    {
        $requester = $leaveRequest->user;
        $leaveType = $leaveRequest->leaveType;
        $requesterName = ($requester->rank ?? '') . ' ' . $requester->name;

        $text = "📋 <b>คำขอลาใหม่</b>\n\n"
            . "👤 ผู้ขอลา: <b>{$requesterName}</b>\n"
            . "📝 ประเภท: {$leaveType->name}\n"
            . "📅 วันที่: {$leaveRequest->start_date->format('d/m/Y')} - {$leaveRequest->end_date->format('d/m/Y')}\n"
            . "⏱ จำนวน: {$leaveRequest->total_days} วัน\n"
            . "💬 เหตุผล: " . ($leaveRequest->reason ?: '-') . "\n"
            . "🔖 สถานะ: " . $this->translateStatus($leaveRequest->status);

        $keyboard = [
            [
                ['text' => '✅ อนุมัติ', 'callback_data' => "approve:{$leaveRequest->id}"],
                ['text' => '❌ ไม่อนุมัติ', 'callback_data' => "reject:{$leaveRequest->id}"],
            ],
        ];

        return $this->sendMessage($approverChatId, $text, $keyboard);
    }

    /**
     * Notify the requester about their leave status change.
     */
    public function notifyLeaveStatusChanged($leaveRequest, $status, $actor, $requesterChatId): bool
    {
        $actorName = ($actor->rank ?? '') . ' ' . $actor->name;
        $leaveType = $leaveRequest->leaveType;

        $emoji = match ($status) {
            'approved' => '✅',
            'rejected' => '❌',
            'pending_director' => '⏳',
            'pending_deputy_director' => '⏳',
            'pending_manager' => '⏳',
            default => '📋',
        };

        $statusText = $this->translateStatus($status);

        $text = "{$emoji} <b>อัปเดตสถานะการลา</b>\n\n"
            . "📝 ประเภท: {$leaveType->name}\n"
            . "📅 วันที่: {$leaveRequest->start_date->format('d/m/Y')} - {$leaveRequest->end_date->format('d/m/Y')}\n"
            . "⏱ จำนวน: {$leaveRequest->total_days} วัน\n"
            . "🔖 สถานะ: <b>{$statusText}</b>\n"
            . "👤 โดย: {$actorName}";

        return $this->sendMessage($requesterChatId, $text);
    }

    /**
     * Send daily leave summary to admins/directors.
     */
    public function sendDailySummary(string $chatId, string $summaryText): bool
    {
        return $this->sendMessage($chatId, $summaryText);
    }

    /**
     * Send duty roster notification to assigned personnel.
     */
    public function sendDutyRosterNotification(string $chatId, string $rosterText): bool
    {
        return $this->sendMessage($chatId, $rosterText);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    protected function translateStatus(string $status): string
    {
        return match ($status) {
            'pending_supervisor' => 'รอหัวหน้าอนุญาต',
            'pending_head' => 'รอหัวหน้าแผนกอนุญาต',
            'pending_manager' => 'รอผู้บังคับบัญชาอนุมัติ',
            'pending_deputy_director' => 'รอรองผอ.รับทราบ',
            'pending_director' => 'รอผอ.อนุมัติ',
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ไม่อนุมัติ',
            'cancelled' => 'ยกเลิก',
            default => $status,
        };
    }

    protected function post(string $method, array $payload): bool
    {
        if (empty($this->botToken)) {
            return false;
        }

        try {
            $response = Http::timeout(15)
                ->connectTimeout(5)
                ->post($this->apiBase . $this->botToken . '/' . $method, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error("[Telegram] {$method} failed: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("[Telegram] {$method} exception: " . $e->getMessage());
            return false;
        }
    }
}
