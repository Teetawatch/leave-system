<?php

namespace App\Console\Commands;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TelegramDailySummary extends Command
{
    protected $signature = 'telegram:daily-summary';
    protected $description = 'ส่งสรุปการลาประจำวันผ่าน Telegram ให้ผู้บริหารและ Admin';

    public function handle(): int
    {
        $telegram = new TelegramService();
        $today = now();
        $todayStr = $today->locale('th')->translatedFormat('l j F Y');

        // 1. People on leave today
        $onLeaveToday = LeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->with(['user', 'leaveType'])
            ->get();

        // 2. Pending requests
        $pendingCount = LeaveRequest::whereNotIn('status', ['approved', 'rejected', 'cancelled'])->count();

        // 3. New requests today
        $newToday = LeaveRequest::whereDate('created_at', $today)->count();

        // 4. Approved today
        $approvedToday = LeaveRequest::where('status', 'approved')
            ->whereDate('updated_at', $today)
            ->count();

        // Build summary text
        $text = "📊 <b>สรุปการลาประจำวัน</b>\n"
            . "📅 {$todayStr}\n"
            . "━━━━━━━━━━━━━━━━━━\n\n";

        // On leave today
        $text .= "🏖 <b>ผู้ลาวันนี้ ({$onLeaveToday->count()} คน)</b>\n";
        if ($onLeaveToday->isEmpty()) {
            $text .= "   ไม่มี\n";
        } else {
            foreach ($onLeaveToday as $lr) {
                $name = ($lr->user->rank ?? '') . ' ' . $lr->user->name;
                $text .= "   • {$name} — {$lr->leaveType->name}\n";
            }
        }

        $text .= "\n📈 <b>สถิติวันนี้</b>\n"
            . "   • ใบลาใหม่: {$newToday} ใบ\n"
            . "   • อนุมัติวันนี้: {$approvedToday} ใบ\n"
            . "   • รอดำเนินการ: {$pendingCount} ใบ\n";

        // 5. Pending breakdown by status
        $pendingByStatus = LeaveRequest::whereNotIn('status', ['approved', 'rejected', 'cancelled'])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        if ($pendingByStatus->isNotEmpty()) {
            $text .= "\n📋 <b>รอดำเนินการแยกตามขั้นตอน</b>\n";
            $statusLabels = [
                'pending_supervisor' => 'รอหัวหน้าอนุญาต',
                'pending_head' => 'รอหัวหน้าแผนกอนุญาต',
                'pending_manager' => 'รอผู้บังคับบัญชาอนุมัติ',
                'pending_deputy_director' => 'รอรองผอ.รับทราบ',
                'pending_director' => 'รอผอ.อนุมัติ',
            ];
            foreach ($pendingByStatus as $status => $count) {
                $label = $statusLabels[$status] ?? $status;
                $text .= "   • {$label}: {$count} ใบ\n";
            }
        }

        // Send to admins, directors, deputy_directors who have telegram linked
        $recipients = User::whereIn('role', ['admin', 'director', 'deputy_director'])
            ->whereNotNull('telegram_chat_id')
            ->get();

        $sentCount = 0;
        foreach ($recipients as $user) {
            if ($telegram->sendDailySummary($user->telegram_chat_id, $text)) {
                $sentCount++;
            }
        }

        $this->info("Daily summary sent to {$sentCount} recipients.");
        Log::info("[Telegram] Daily summary sent to {$sentCount} recipients");

        return Command::SUCCESS;
    }
}
