<?php

namespace App\Http\Controllers;

use App\Models\DutyRoster;
use App\Models\LeaveRequest;
use App\Models\SeniorDutyRoster;
use App\Models\User;
use App\Services\LeaveApprovalService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramBotController extends Controller
{
    protected TelegramService $telegram;
    protected LeaveApprovalService $approvalService;

    public function __construct(TelegramService $telegram, LeaveApprovalService $approvalService)
    {
        $this->telegram = $telegram;
        $this->approvalService = $approvalService;
    }

    /**
     * Webhook endpoint for Telegram Bot updates.
     */
    public function webhook(Request $request)
    {
        // Verify secret token from header
        $secretToken = config('services.telegram.webhook_secret');
        if ($request->header('X-Telegram-Bot-Api-Secret-Token') !== $secretToken) {
            Log::warning('[Telegram Webhook] Invalid secret token');
            return response('Unauthorized', 401);
        }

        $update = $request->all();

        Log::info('[Telegram Webhook] Received update', ['update_id' => $update['update_id'] ?? null]);

        // Handle callback query (inline button press)
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
            return response('OK', 200);
        }

        // Handle regular message (commands)
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
            return response('OK', 200);
        }

        return response('OK', 200);
    }

    /**
     * Handle inline keyboard button presses (Approve/Reject).
     */
    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $callbackId = $callbackQuery['id'];
        $chatId = $callbackQuery['from']['id'] ?? null;
        $messageId = $callbackQuery['message']['message_id'] ?? null;
        $data = $callbackQuery['data'] ?? '';

        // Find the user by telegram_chat_id
        $user = User::where('telegram_chat_id', (string) $chatId)->first();

        if (!$user) {
            $this->telegram->answerCallbackQuery($callbackId, 'กรุณาเชื่อมต่อบัญชีก่อน (/link)', true);
            return;
        }

        // Parse callback data: "approve:123" or "reject:123"
        $parts = explode(':', $data, 2);
        if (count($parts) !== 2) {
            $this->telegram->answerCallbackQuery($callbackId, 'คำสั่งไม่ถูกต้อง', true);
            return;
        }

        [$action, $leaveRequestId] = $parts;

        $leaveRequest = LeaveRequest::with(['user', 'leaveType'])->find($leaveRequestId);

        if (!$leaveRequest) {
            $this->telegram->answerCallbackQuery($callbackId, 'ไม่พบใบลานี้ในระบบ', true);
            return;
        }

        // Check if already processed
        if (in_array($leaveRequest->status, ['approved', 'rejected', 'cancelled'])) {
            $this->telegram->answerCallbackQuery($callbackId, 'ใบลานี้ดำเนินการไปแล้ว', true);
            $this->updateMessageAfterAction($chatId, $messageId, $leaveRequest, 'ดำเนินการไปแล้ว');
            return;
        }

        try {
            if ($action === 'approve') {
                $this->approvalService->approve($leaveRequest, $user, 'อนุมัติผ่าน Telegram Bot');
                $this->telegram->answerCallbackQuery($callbackId, '✅ อนุมัติเรียบร้อย');
                $this->updateMessageAfterAction($chatId, $messageId, $leaveRequest->fresh(['user', 'leaveType']), '✅ อนุมัติแล้ว โดย ' . $user->name);
            } elseif ($action === 'reject') {
                $this->approvalService->reject($leaveRequest, $user, 'ไม่อนุมัติผ่าน Telegram Bot');
                $this->telegram->answerCallbackQuery($callbackId, '❌ ปฏิเสธเรียบร้อย');
                $this->updateMessageAfterAction($chatId, $messageId, $leaveRequest->fresh(['user', 'leaveType']), '❌ ไม่อนุมัติ โดย ' . $user->name);
            } else {
                $this->telegram->answerCallbackQuery($callbackId, 'คำสั่งไม่ถูกต้อง', true);
            }
        } catch (\Exception $e) {
            Log::error('[Telegram] Callback action error: ' . $e->getMessage());
            $this->telegram->answerCallbackQuery($callbackId, 'เกิดข้อผิดพลาด: ' . $e->getMessage(), true);
        }
    }

    /**
     * Handle regular text messages / commands.
     */
    protected function handleMessage(array $message): void
    {
        $chatId = (string) ($message['chat']['id'] ?? '');
        $text = trim($message['text'] ?? '');

        if (str_starts_with($text, '/start')) {
            $this->handleStartCommand($chatId, $text);
            return;
        }

        if ($text === '/link') {
            $this->handleLinkCommand($chatId);
            return;
        }

        if ($text === '/unlink') {
            $this->handleUnlinkCommand($chatId);
            return;
        }

        if ($text === '/status') {
            $this->handleStatusCommand($chatId);
            return;
        }

        if ($text === '/pending') {
            $this->handlePendingCommand($chatId);
            return;
        }

        if ($text === '/today') {
            $this->handleTodayCommand($chatId);
            return;
        }

        if ($text === '/duty') {
            $this->handleDutyCommand($chatId);
            return;
        }

        if ($text === '/help') {
            $this->handleHelpCommand($chatId);
            return;
        }

        // Unknown command
        $this->telegram->sendMessage(
            $chatId,
            "❓ ไม่เข้าใจคำสั่ง พิมพ์ /help เพื่อดูคำสั่งที่ใช้ได้"
        );
    }

    // =========================================================================
    // Command Handlers
    // =========================================================================

    protected function handleStartCommand(string $chatId, string $text): void
    {
        // /start <link_token> — auto-link from profile page
        // Log the text with delimiters to see exact content including spaces
        Log::info("[Telegram Bot] handleStartCommand text: [{$text}]");
        
        // Match /start followed by token. Case-insensitive match for the regex
        if (preg_match('/^\/start\s+([a-zA-Z0-9_\-]+)/i', $text, $matches)) {
            // Trim and convert to lowercase for consistent matching
            $linkToken = strtolower(trim($matches[1]));
            Log::info("[Telegram Bot] Normalized linking token: [{$linkToken}] from chatId: [{$chatId}]");
            
            // First, find the user with this token
            $user = User::where('telegram_link_token', $linkToken)->first();

            if ($user) {
                // Use explicit assignment and save() for more reliable persistence
                $user->telegram_chat_id = (string) $chatId;
                $user->telegram_link_token = null;
                $saved = $user->save();

                if ($saved) {
                    Log::info("[Telegram Bot] Successfully linked and saved User ID: [{$user->id}] with Chat ID: [{$chatId}]");
                } else {
                    Log::error("[Telegram Bot] Database FAILED to save linking for User ID: [{$user->id}]");
                }

                $this->telegram->sendMessage(
                    $chatId,
                    "✅ เชื่อมต่อสำเร็จ!\n\n"
                        . "👤 บัญชี: <b>{$user->rank} {$user->name}</b>\n"
                        . "📌 ตำแหน่ง: {$user->position}\n\n"
                        . "คุณจะได้รับแจ้งเตือนการลา/อนุมัติผ่าน Telegram แล้ว\n"
                        . "พิมพ์ /help เพื่อดูคำสั่งทั้งหมด"
                );
                return;
            }

            // Fallback: Check if the chatId is already linked
            $alreadyLinked = User::where('telegram_chat_id', $chatId)->first();
            if ($alreadyLinked) {
                Log::info("[Telegram Bot] Chat ID already linked to User ID: [{$alreadyLinked->id}]");
                $this->telegram->sendMessage(
                    $chatId,
                    "👋 สวัสดี <b>{$alreadyLinked->rank} {$alreadyLinked->name}</b>!\n\n"
                        . "บัญชีของคุณได้รับการเชื่อมต่อเรียบร้อยแล้ว\n"
                        . "พิมพ์ /help เพื่อดูคำสั่ง"
                );
                return;
            }

            Log::warning("[Telegram Bot] Token not found in database: [{$linkToken}] for Chat ID: [{$chatId}]");
            $this->telegram->sendMessage(
                $chatId,
                "❌ ลิงก์หมดอายุหรือไม่ถูกต้อง กรุณาสร้างลิงก์ใหม่จากหน้าโปรไฟล์"
            );
            return;
        }

        // Plain /start or fallback
        Log::info("[Telegram Bot] Fallback to plain /start for Chat ID: [{$chatId}] with text: [{$text}]");
        $user = User::where('telegram_chat_id', $chatId)->first();
        if ($user) {
            $this->telegram->sendMessage(
                $chatId,
                "👋 สวัสดี <b>{$user->rank} {$user->name}</b>!\n\n"
                . "บัญชีเชื่อมต่อแล้ว พิมพ์ /help เพื่อดูคำสั่ง"
            );
        } else {
            $this->telegram->sendMessage(
                $chatId,
                "👋 สวัสดี! ยินดีต้อนรับสู่ระบบแจ้งเตือนการลา NASS\n\n"
                . "กรุณาเชื่อมต่อบัญชีผ่านหน้าโปรไฟล์ในระบบ\n"
                . "หรือพิมพ์ /link เพื่อเชื่อมต่อด้วยรหัส"
            );
        }
    }

    protected function handleLinkCommand(string $chatId): void
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        if ($user) {
            $this->telegram->sendMessage(
                $chatId,
                "✅ บัญชีเชื่อมต่อแล้ว: <b>{$user->rank} {$user->name}</b>\n"
                . "หากต้องการเปลี่ยนบัญชี พิมพ์ /unlink ก่อน"
            );
            return;
        }

        $this->telegram->sendMessage(
            $chatId,
            "🔗 กรุณาเชื่อมต่อบัญชีจากหน้าโปรไฟล์ในระบบ\n\n"
            . "ไปที่ <b>โปรไฟล์ → เชื่อมต่อ Telegram</b>\n"
            . "กดปุ่ม \"เชื่อมต่อ Telegram\" แล้วระบบจะสร้างลิงก์ให้\n\n"
            . "🆔 Chat ID ของคุณ: <code>{$chatId}</code>"
        );
    }

    protected function handleUnlinkCommand(string $chatId): void
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        if (!$user) {
            $this->telegram->sendMessage($chatId, "❌ ไม่มีบัญชีเชื่อมต่ออยู่");
            return;
        }

        $user->update(['telegram_chat_id' => null]);
        $this->telegram->sendMessage(
            $chatId,
            "✅ ยกเลิกการเชื่อมต่อเรียบร้อย\n"
            . "คุณจะไม่ได้รับแจ้งเตือนผ่าน Telegram อีก"
        );
    }

    protected function handleStatusCommand(string $chatId): void
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        if (!$user) {
            $this->telegram->sendMessage($chatId, "❌ กรุณาเชื่อมต่อบัญชีก่อน (/link)");
            return;
        }

        $myPending = LeaveRequest::where('user_id', $user->id)
            ->whereNotIn('status', ['approved', 'rejected', 'cancelled'])
            ->with('leaveType')
            ->latest()
            ->take(5)
            ->get();

        if ($myPending->isEmpty()) {
            $this->telegram->sendMessage($chatId, "✅ ไม่มีใบลาที่รอดำเนินการ");
            return;
        }

        $text = "📋 <b>ใบลาของคุณที่รอดำเนินการ</b>\n\n";
        foreach ($myPending as $i => $lr) {
            $num = $i + 1;
            $status = $this->translateStatus($lr->status);
            $text .= "{$num}. {$lr->leaveType->name}\n"
                . "   📅 {$lr->start_date->format('d/m/Y')} - {$lr->end_date->format('d/m/Y')}\n"
                . "   🔖 {$status}\n\n";
        }

        $this->telegram->sendMessage($chatId, $text);
    }

    protected function handlePendingCommand(string $chatId): void
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        if (!$user) {
            $this->telegram->sendMessage($chatId, "❌ กรุณาเชื่อมต่อบัญชีก่อน (/link)");
            return;
        }

        // Get leave requests pending this user's action
        $pendingRequests = $this->getPendingForApprover($user);

        if ($pendingRequests->isEmpty()) {
            $this->telegram->sendMessage($chatId, "✅ ไม่มีใบลาที่รอการอนุมัติจากคุณ");
            return;
        }

        $studentCourses = ['หลักสูตรนายทหารพลาธิการชั้นนายเรือ ประจำปีงบประมาณ 69', 'หลักสูตรอาชีพเพื่อเลื่อนฐานะชั้น จ.อ.'];
        $pendingCivil = $pendingRequests->filter(fn($lr) => !in_array($lr->user->department ?? '', $studentCourses));
        $pendingCourse = $pendingRequests->filter(fn($lr) => in_array($lr->user->department ?? '', $studentCourses));

        $text = "📋 <b>ใบลาที่รอการอนุมัติจากคุณ</b> ({$pendingRequests->count()} ใบ)\n"
            . "━━━━━━━━━━━━━━━━━━\n";

        $renderPending = function ($items, string $label) use (&$text) {
            if ($items->isEmpty())
                return;
            $text .= "\n{$label} ({$items->count()} ใบ)\n";
            foreach ($items->values() as $i => $lr) {
                $num = $i + 1;
                $requesterName = ($lr->user->rank ?? '') . ' ' . $lr->user->name;
                $startStr = $lr->start_date->format('d/m/Y');
                $endStr = $lr->end_date->format('d/m/Y');

                $periodLabel = '';
                if ($lr->temporary_leave_period === 'morning') {
                    $periodLabel = ' (ช่วงเช้า)';
                } elseif ($lr->temporary_leave_period === 'afternoon') {
                    $periodLabel = ' (ช่วงบ่าย)';
                }

                $text .= "{$num}. {$requesterName}\n"
                    . "   📝 {$lr->leaveType->name}{$periodLabel} ({$lr->total_days} วัน)\n"
                    . "   📅 {$startStr} — {$endStr}\n\n";
            }
        };

        $renderPending($pendingCivil, '👔 <b>ข้าราชการ</b>');
        $renderPending($pendingCourse, '🎓 <b>หลักสูตร</b>');

        $this->telegram->sendMessage($chatId, $text);

        // Send each with approve/reject buttons
        foreach ($pendingRequests as $lr) {
            $this->telegram->notifyNewLeaveRequest($lr, $chatId);
        }
    }

    protected function handleTodayCommand(string $chatId): void
    {
        $today = now();
        $todayStr = $this->formatThaiDate($today);

        $onLeave = LeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->with(['user', 'leaveType'])
            ->get();

        if ($onLeave->isEmpty()) {
            $this->telegram->sendMessage(
                $chatId,
                "📅 <b>ข้อมูลการจำหน่ายกำลังพล ประจำวันที่ {$todayStr}</b>\n\n✅ ไม่มีผู้ลาในวันนี้"
            );
            return;
        }

        $studentCourses = ['หลักสูตรนายทหารพลาธิการชั้นนายเรือ ประจำปีงบประมาณ 69', 'หลักสูตรอาชีพเพื่อเลื่อนฐานะชั้น จ.อ.'];
        $onLeaveCivil = $onLeave->filter(fn($lr) => !in_array($lr->user->department ?? '', $studentCourses));
        $onLeaveCourse = $onLeave->filter(fn($lr) => in_array($lr->user->department ?? '', $studentCourses));

        $text = "📅 <b>ผู้ลาประจำวันที่ {$todayStr}</b>\n"
            . "👥 รวม {$onLeave->count()} นาย\n"
            . "━━━━━━━━━━━━━━━━━━\n";

        $renderGroup = function ($items, string $label) use (&$text) {
            if ($items->isEmpty())
                return;
            $text .= "\n{$label} ({$items->count()} คน)\n";
            foreach ($items->values() as $i => $lr) {
                $num = $i + 1;
                $name = ($lr->user->rank ?? '') . ' ' . $lr->user->name;
                $startStr = $this->formatThaiDate($lr->start_date);
                $endStr = $this->formatThaiDate($lr->end_date);
                $isTemporary = $lr->leaveType->slug === 'temporary';

                $text .= "{$num}. <b>{$name}</b>\n"
                    . "   📝 {$lr->leaveType->name}";

                if ($isTemporary) {
                    $period = match ($lr->temporary_leave_period) {
                        'morning' => ' (ช่วงเช้า)',
                        'afternoon' => ' (ช่วงบ่าย)',
                        default => '',
                    };
                    $text .= "{$period}\n"
                        . "   📆 {$startStr}\n";
                } elseif ($lr->start_date->isSameDay($lr->end_date)) {
                    $text .= "\n   📆 {$startStr} ({$lr->total_days} วัน)\n";
                } else {
                    $text .= "\n   📆 {$startStr} — {$endStr} ({$lr->total_days} วัน)\n";
                }

                $text .= "\n";
            }
        };

        $renderGroup($onLeaveCivil, '👔 <b>ข้าราชการ</b>');
        $renderGroup($onLeaveCourse, '🎓 <b>หลักสูตร</b>');

        $this->telegram->sendMessage($chatId, $text);
    }

    protected function handleDutyCommand(string $chatId): void
    {
        $today = now();
        $todayStr = $this->formatThaiDate($today);

        $roster = DutyRoster::with([
            'dutyOfficer',
            'reserveDutyOfficer',
            'assistantDutyOfficer',
            'reserveAssistantDutyOfficer',
        ])->whereDate('duty_date', $today)->first();

        $seniorRoster = SeniorDutyRoster::with('seniorOfficer')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if (!$roster && !$seniorRoster) {
            $this->telegram->sendMessage(
                $chatId,
                "🛡 <b>ตารางเวรประจำวันที่ {$todayStr}</b>\n\n❌ ไม่มีข้อมูลตารางเวรในวันนี้"
            );
            return;
        }

        $text = "🛡 <b>ตารางเวรประจำวันที่ {$todayStr}</b>\n"
            . "━━━━━━━━━━━━━━━━━━\n\n";

        if ($seniorRoster && $seniorRoster->seniorOfficer) {
            $name = ($seniorRoster->seniorOfficer->rank ?? '') . ' ' . $seniorRoster->seniorOfficer->name;
            $text .= "⭐ <b>นายทหารเวรอาวุโส</b>\n   {$name}\n\n";
        }

        if ($roster) {
            $roles = [
                'นายทหารเวร' => $roster->dutyOfficer,
                'นายทหารเวรสำรอง' => $roster->reserveDutyOfficer,
                'ผู้ช่วยนายทหารเวร' => $roster->assistantDutyOfficer,
                'ผู้ช่วยนายทหารเวรสำรอง' => $roster->reserveAssistantDutyOfficer,
            ];

            foreach ($roles as $roleName => $user) {
                if ($user) {
                    $name = ($user->rank ?? '') . ' ' . $user->name;
                    $text .= "👤 <b>{$roleName}</b>\n   {$name}\n\n";
                }
            }

            if ($roster->notes) {
                $text .= "📝 หมายเหตุ: {$roster->notes}\n";
            }
        }

        $this->telegram->sendMessage($chatId, $text);
    }

    protected function handleHelpCommand(string $chatId): void
    {
        $text = "📖 <b>คำสั่งที่ใช้ได้</b>\n\n"
            . "/start - เริ่มต้นใช้งาน\n"
            . "/link - เชื่อมต่อบัญชี\n"
            . "/unlink - ยกเลิกการเชื่อมต่อ\n"
            . "/status - ดูสถานะใบลาของตัวเอง\n"
            . "/pending - ดูใบลาที่รออนุมัติจากคุณ\n"
            . "/today - ดูรายชื่อผู้ลาประจำวัน\n"
            . "/duty - ดูผู้เข้าเวรประจำวัน\n"
            . "/help - แสดงคำสั่งนี้\n\n"
            . "💡 เมื่อมีใบลาใหม่ ระบบจะส่งแจ้งเตือนพร้อมปุ่ม อนุมัติ/ไม่อนุมัติ ให้อัตโนมัติ";

        $this->telegram->sendMessage($chatId, $text);
    }

    // =========================================================================
    // Artisan Commands: Setup Webhook
    // =========================================================================

    /**
     * Setup webhook (called from artisan command or route).
     */
    public function setupWebhook(Request $request)
    {
        $url = $request->input('url');
        if (!$url) {
            $url = url('/telegram/webhook/' . config('services.telegram.webhook_secret'));
        }

        $secret = config('services.telegram.webhook_secret');
        $result = $this->telegram->setWebhook($url, $secret);

        return response()->json([
            'success' => $result,
            'webhook_url' => $url,
        ]);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    protected function updateMessageAfterAction(string $chatId, ?int $messageId, LeaveRequest $leaveRequest, string $resultText): void
    {
        if (!$messageId)
            return;

        $requester = $leaveRequest->user;
        $requesterName = ($requester->rank ?? '') . ' ' . $requester->name;
        $leaveType = $leaveRequest->leaveType;

        $text = "📋 <b>คำขอลา</b>\n\n"
            . "👤 ผู้ขอลา: <b>{$requesterName}</b>\n"
            . "📝 ประเภท: {$leaveType->name}\n"
            . "📅 วันที่: {$leaveRequest->start_date->format('d/m/Y')} - {$leaveRequest->end_date->format('d/m/Y')}\n"
            . "⏱ จำนวน: {$leaveRequest->total_days} วัน\n\n"
            . "🏷 <b>{$resultText}</b>";

        $this->telegram->editMessageText($chatId, $messageId, $text);
    }

    protected function getPendingForApprover(User $user)
    {
        $query = LeaveRequest::with(['user', 'leaveType']);

        if ($user->role === 'admin') {
            return $query->whereNotIn('status', ['approved', 'rejected', 'cancelled'])
                ->latest()->take(10)->get();
        }

        if ($user->role === 'director') {
            return $query->where('status', 'pending_director')
                ->latest()->take(10)->get();
        }

        if ($user->role === 'deputy_director') {
            return $query->where('status', 'pending_deputy_director')
                ->latest()->take(10)->get();
        }

        // Supervisor / Department head
        $subordinateIds = User::where('supervisor_id', $user->id)->pluck('id');
        $deptIds = collect();
        if ($user->role === 'department_head' && $user->department) {
            $deptIds = User::where('department', $user->department)->pluck('id');
        }

        $allIds = $subordinateIds->merge($deptIds)->unique();

        // Manager
        $managedIds = User::where('manager_id', $user->id)->pluck('id');

        return $query->where(function ($q) use ($allIds, $managedIds) {
            $q->where(function ($qq) use ($allIds) {
                $qq->whereIn('status', ['pending_supervisor', 'pending_head'])
                    ->whereIn('user_id', $allIds);
            })->orWhere(function ($qq) use ($managedIds) {
                $qq->where('status', 'pending_manager')
                    ->whereIn('user_id', $managedIds);
            });
        })->latest()->take(10)->get();
    }

    protected function formatThaiDate(\Carbon\Carbon $date): string
    {
        $thaiMonths = [
            1 => 'ม.ค.',
            2 => 'ก.พ.',
            3 => 'มี.ค.',
            4 => 'เม.ย.',
            5 => 'พ.ค.',
            6 => 'มิ.ย.',
            7 => 'ก.ค.',
            8 => 'ส.ค.',
            9 => 'ก.ย.',
            10 => 'ต.ค.',
            11 => 'พ.ย.',
            12 => 'ธ.ค.',
        ];
        $day = $date->day;
        $month = $thaiMonths[$date->month];
        $year = $date->year + 543;
        return "{$day} {$month}{$year}";
    }

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
}
