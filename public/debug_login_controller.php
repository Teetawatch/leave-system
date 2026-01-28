<?php
// debug_login_controller.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/../../leave-core/vendor/autoload.php';
$app = require_once __DIR__.'/../../leave-core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // 1. จำลอง User
    $user = \App\Models\User::where('email', 'teetawatch@gmail.com')->first();
    
    // 2. ลอง Load Relationship (ถ้าพังจะ Error ตรงนี้)
    echo "Testing Load Relationships... ";
    $user->load(['supervisor', 'manager']);
    echo "OK!<br>";

    // 3. ลองเรียก UserResource (จุดปราบเซียน)
    echo "Testing UserResource... ";
    $resource = new \App\Http\Resources\UserResource($user);
    $data = $resource->toArray(request());
    echo "OK!<br>";
    
    // 4. ดูผลลัพธ์
    echo "<pre>";
    print_r($data);
    echo "</pre>";

} catch (\Exception $e) {
    echo "<h1>❌ Error Found!</h1>";
    echo "Message: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")";
}