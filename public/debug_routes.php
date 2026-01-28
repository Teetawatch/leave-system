<?php

// 1. เช็คว่าโหลด Laravel ขึ้นไหม
require __DIR__ . '/../../leave-core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../leave-core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 2. แสดงรายการ Route ทั้งหมดที่ Laravel รู้จัก
$routes = Illuminate\Support\Facades\Route::getRoutes();

echo "<h1>Laravel Routes Check</h1>";
echo "<p>ระบบโหลด Laravel ได้สำเร็จ!</p>";
echo "<table border='1'><tr><th>Method</th><th>URI</th><th>Name</th></tr>";

foreach ($routes as $route) {
    echo "<tr>";
    echo "<td>" . implode('|', $route->methods()) . "</td>";
    echo "<td>" . $route->uri() . "</td>";
    echo "<td>" . $route->getName() . "</td>";
    echo "</tr>";
}
echo "</table>";