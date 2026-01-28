<?php
// แสดง Error ทั้งหมดออกมาทางหน้าจอ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require __DIR__.'/../../leave-core/vendor/autoload.php';
    $app = require_once __DIR__.'/../../leave-core/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "<h3>1. Database Check</h3>";
    $email = 'teetawatch@gmail.com'; // อีเมลที่คุณใช้ทดสอบ
    $user = \App\Models\User::where('email', $email)->first();
    
    if (!$user) {
        die("Error: ไม่พบ User ในฐานข้อมูล");
    }
    echo "พบ User ID: " . $user->id . "<br>";

    echo "<h3>2. Token Generation Check (Sanctum)</h3>";
    // ส่วนนี้มักจะทำให้เกิด Error 500 ถ้า Database ไม่ครบ
    $token = $user->createToken('DebugDevice')->plainTextToken;
    echo "สร้าง Token สำเร็จ: " . substr($token, 0, 10) . "...<br>";

    echo "<h3>✅ สรุป: ระบบ Login ปกติ (ปัญหาน่าจะอยู่ที่ Controller หรือ Route)</h3>";

} catch (\Exception $e) {
    echo "<h1>❌ เกิด Error 500!</h1>";
    echo "<strong>Message:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>File:</strong> " . $e->getFile() . " (Line: " . $e->getLine() . ")<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}