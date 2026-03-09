<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LineService;
use Illuminate\Support\Facades\Log;

class CheckLineStatus extends Command
{
    protected $signature = 'line:check-status';
    protected $description = 'ตรวจสอบสถานะ LINE API และ quota';

    protected $lineService;

    public function __construct(LineService $lineService)
    {
        parent::__construct();
        $this->lineService = $lineService;
    }

    public function handle()
    {
        $this->info('🔍 กำลังตรวจสอบสถานะ LINE API...');

        $token        = env('LINE_CHANNEL_ACCESS_TOKEN');
        $groupId      = env('LINE_GROUP_ID');
        $notifyToken  = env('LINE_NOTIFY_TOKEN');
        $monthlyLimit = (int) env('LINE_MONTHLY_QUOTA', 500);

        $this->line('📋 ข้อมูลการตั้งค่า:');
        $this->line('   Channel Access Token : ' . ($token       ? '✅ ตั้งค่าแล้ว' : '❌ ไม่ได้ตั้งค่า'));
        $this->line('   Group ID             : ' . ($groupId     ? '✅ ตั้งค่าแล้ว' : '❌ ไม่ได้ตั้งค่า'));
        $this->line('   LINE Notify Token    : ' . ($notifyToken ? '✅ ตั้งค่าแล้ว' : '⚠️  ไม่ได้ตั้งค่า (ข้อความกลุ่มจะใช้ push)'));

        if (!$token) {
            $this->error('❌ กรุณาตั้งค่า LINE_CHANNEL_ACCESS_TOKEN ใน .env');
            return Command::FAILURE;
        }

        // Quota usage from local cache
        $this->newLine();
        $this->info('📊 การใช้งาน Push Quota เดือนนี้:');
        $monthKey    = 'line_push_quota_' . now()->format('Y_m');
        $localUsed   = (int) cache()->get($monthKey, 0);
        $localRemain = max(0, $monthlyLimit - $localUsed);
        $percent     = $monthlyLimit > 0 ? round(($localUsed / $monthlyLimit) * 100, 1) : 0;

        $this->line("   ใช้ไปแล้ว (local): {$localUsed}/{$monthlyLimit} ({$percent}%)");
        $this->line("   เหลืออยู่ (local): {$localRemain}");

        // Quota from LINE API
        try {
            $apiRemaining = $this->lineService->getRemainingQuota();
            if ($apiRemaining === -1) {
                $this->line('   เหลืออยู่ (LINE API): ไม่จำกัด (paid plan)');
            } else {
                $this->line("   เหลืออยู่ (LINE API): {$apiRemaining}");
            }
        } catch (\Exception $e) {
            $this->line('   เหลืออยู่ (LINE API): ไม่สามารถตรวจสอบได้');
        }

        if ($percent >= 80) {
            $this->warn("⚠️  ใช้ quota ไปแล้ว {$percent}% — ระวังเกิน 500/เดือน");
            $this->warn('   ข้อความกลุ่มควรใช้ LINE Notify (LINE_NOTIFY_TOKEN) แทน push');
        }

        if (!$groupId) {
            $this->warn('⚠️  LINE_GROUP_ID ไม่ได้ตั้งค่า — ข้ามการทดสอบส่งกลุ่ม');
            return Command::SUCCESS;
        }

        // Test API connection
        $this->newLine();
        $this->info('🧪 ทดสอบการเชื่อมต่อ API...');

        try {
            $testMessage = '🧪 ทดสอบการเชื่อมต่อ LINE API - ' . now()->format('H:i:s');
            $result = $this->lineService->sendGroupTextMessage($testMessage);

            if ($result) {
                $this->info('✅ LINE API ทำงานปกติ ส่งข้อความทดสอบสำเร็จ');
            } else {
                $this->error('❌ ไม่สามารถส่งข้อความได้ — อาจเกิน quota หรือมีปัญหาอื่นๆ');
            }
        } catch (\Exception $e) {
            $this->error('❌ เกิดข้อผิดพลาด: ' . $e->getMessage());

            if (str_contains($e->getMessage(), '429')) {
                $this->warn('⚠️  เกิด 429 Too Many Requests — quota หมดแล้ว');
            } elseif (str_contains($e->getMessage(), '401')) {
                $this->warn('⚠️  Authentication failed (401) — Access Token ไม่ถูกต้องหรือหมดอายุ');
            } elseif (str_contains($e->getMessage(), '403')) {
                $this->warn('⚠️  Forbidden (403) — Bot อาจถูกระงับ');
            }
        }

        $this->newLine();
        $this->info('💡 วิธีประหยัด quota (Free tier 500/เดือน):');
        $this->line('   1. ตั้งค่า LINE_NOTIFY_TOKEN — ข้อความกลุ่มใช้ LINE Notify (ฟรีไม่จำกัด)');
        $this->line('   2. ข้อความแจ้งเตือน push จะส่งเฉพาะบุคคล (ผู้ขอ/ผู้อนุมัติ) เท่านั้น');
        $this->line('   3. ตรวจสอบ LINE Developers Console > Messaging API > Statistics');
        $this->line('   4. ออก LINE Notify token ได้ที่ https://notify-bot.line.me/');

        return Command::SUCCESS;
    }
}
