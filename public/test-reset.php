<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// โหลดระบบ Laravel
require __DIR__.'/../../leave-core/vendor/autoload.php';
$app = require_once __DIR__.'/../../leave-core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// แก้ไขรหัสผ่านของ User ที่ต้องการ
$email = 'teetawatch@gmail.com'; // แก้เป็นอีเมลของคุณ
$user = User::where('email', $email)->first();

if ($user) {
    $user->password = Hash::make('123456'); // ตั้งใหม่เป็น 123456
    $user->save();
    echo "สำเร็จ! รหัสผ่านถูกเปลี่ยนเป็น 123456 แล้ว";
} else {
    echo "ไม่พบอีเมลนี้ในระบบ";
}