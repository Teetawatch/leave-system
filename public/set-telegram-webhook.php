<?php
// set-telegram-webhook.php (วางไว้ที่ public/)
require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->handle();

// รันคำสั่ง set webhook
Artisan::call('telegram:set-webhook');

echo '<h2>✅ Telegram Webhook Set!</h2>';
echo '<pre>' . Artisan::output() . '</pre>';