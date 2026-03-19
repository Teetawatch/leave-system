<?php

namespace App\Console\Commands;

use App\Models\DutyRoster;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TelegramDutyRosterNotify extends Command
{
    protected $signature = 'telegram:duty-roster-notify';
    protected $description = 'ส่งแจ้งเตือนตารางเวรประจำวันผ่าน Telegram';

    public function handle(): int
    {
        $telegram = new TelegramService();
        $today = now();
        $todayStr = $today->locale('th')->translatedFormat('l j F Y');

        // Get today's duty roster
        $roster = DutyRoster::with([
            'dutyOfficer',
            'reserveDutyOfficer',
            'assistantDutyOfficer',
            'reserveAssistantDutyOfficer',
        ])->whereDate('duty_date', $today)->first();

        if (!$roster) {
            $this->info('No duty roster for today.');
            return Command::SUCCESS;
        }

        // Build roster text
        $text = "🛡 <b>ตารางเวรประจำวัน</b>\n"
            . "📅 {$todayStr}\n"
            . "━━━━━━━━━━━━━━━━━━\n\n";

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

        // Send notification to each person on duty
        $sentCount = 0;
        $notifiedUsers = collect();

        foreach ($roles as $roleName => $user) {
            if ($user && !empty($user->telegram_chat_id) && !$notifiedUsers->contains($user->id)) {
                $personalText = "🔔 <b>คุณมีเวรวันนี้!</b>\n"
                    . "📌 ตำแหน่ง: {$roleName}\n\n"
                    . $text;

                if ($telegram->sendDutyRosterNotification($user->telegram_chat_id, $personalText)) {
                    $sentCount++;
                }
                $notifiedUsers->push($user->id);
            }
        }

        // Also send roster to tomorrow's duty personnel as a reminder
        $tomorrow = $today->copy()->addDay();
        $tomorrowRoster = DutyRoster::with([
            'dutyOfficer',
            'reserveDutyOfficer',
            'assistantDutyOfficer',
            'reserveAssistantDutyOfficer',
        ])->whereDate('duty_date', $tomorrow)->first();

        if ($tomorrowRoster) {
            $tomorrowStr = $tomorrow->locale('th')->translatedFormat('l j F Y');
            $reminderText = "⏰ <b>แจ้งเตือนเวรพรุ่งนี้</b>\n"
                . "📅 {$tomorrowStr}\n"
                . "━━━━━━━━━━━━━━━━━━\n\n";

            $tomorrowRoles = [
                'นายทหารเวร' => $tomorrowRoster->dutyOfficer,
                'นายทหารเวรสำรอง' => $tomorrowRoster->reserveDutyOfficer,
                'ผู้ช่วยนายทหารเวร' => $tomorrowRoster->assistantDutyOfficer,
                'ผู้ช่วยนายทหารเวรสำรอง' => $tomorrowRoster->reserveAssistantDutyOfficer,
            ];

            foreach ($tomorrowRoles as $roleName => $user) {
                if ($user) {
                    $name = ($user->rank ?? '') . ' ' . $user->name;
                    $reminderText .= "👤 <b>{$roleName}</b>\n   {$name}\n\n";
                }
            }

            foreach ($tomorrowRoles as $roleName => $user) {
                if ($user && !empty($user->telegram_chat_id) && !$notifiedUsers->contains($user->id)) {
                    $personalReminder = "🔔 <b>คุณมีเวรพรุ่งนี้!</b>\n"
                        . "📌 ตำแหน่ง: {$roleName}\n\n"
                        . $reminderText;

                    if ($telegram->sendDutyRosterNotification($user->telegram_chat_id, $personalReminder)) {
                        $sentCount++;
                    }
                    $notifiedUsers->push($user->id);
                }
            }
        }

        $this->info("Duty roster notifications sent to {$sentCount} personnel.");
        Log::info("[Telegram] Duty roster notifications sent to {$sentCount} personnel");

        return Command::SUCCESS;
    }
}
