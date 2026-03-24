<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramSetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {url? : The webhook URL}';
    protected $description = 'ตั้งค่า Telegram Bot Webhook URL';

    public function handle(): int
    {
        $telegram = new TelegramService();
        $url = $this->argument('url');

if (!$url) {
            // ดึงจากไฟล์ .env
            $secret = (string) config('services.telegram.webhook_secret', 'nass126612'); 
            
            // ใช้ฟังก์ชัน route() ของ Laravel เพื่อแปลเป็น URL ให้ตรงกับ Domain ของโฮสต์แทนการฟิกซ์ตายตัว
            $url = route('telegram.webhook', ['secret' => $secret]);
        }

        $this->info("Setting webhook to: {$url}");

        $secret = (string) config('services.telegram.webhook_secret', '');
        $result = $telegram->setWebhook($url, $secret);

        if ($result) {
            $this->info('✅ Webhook set successfully!');
        } else {
            $this->error('❌ Failed to set webhook. Check your bot token.');
        }

        // Show current webhook info
        $info = $telegram->getWebhookInfo();
        if ($info) {
            $this->table(['Key', 'Value'], collect($info['result'] ?? [])->map(function ($val, $key) {
                return [$key, is_array($val) ? json_encode($val) : (string) $val];
            })->toArray());
        }

        return $result ? Command::SUCCESS : Command::FAILURE;
    }
}
