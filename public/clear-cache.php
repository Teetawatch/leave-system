<?php
require __DIR__.'/../../leave-core/vendor/autoload.php';
$app = require_once __DIR__.'/../../leave-core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

// สั่งรันคำสั่งเคลียร์แคช
Artisan::call('route:clear');
Artisan::call('config:clear');
Artisan::call('cache:clear');
Artisan::call('optimize:clear');

echo "Systems caches cleared!";