# External Monitoring Setup for Shared Hosting

## 1. UptimeRobot (Free)
# เพิ่มใน UptimeRobot:
# URL: https://yourdomain.com/api/line/status?key=secret123
# Check Interval: 5 minutes
# Alert on: HTTP error or JSON response contains "error"

## 2. Pingdom (Free tier available)
# ตั้งคือคล้ายกับ UptimeRobot

## 3. Cron-job.org (Free)
# สำหรับตั้งเวลาเรียก API:
# URL: https://yourdomain.com/api/line/status?key=secret123
# Schedule: Every 30 minutes

## 4. สร้าง PHP Script สำหรับ Cron
# บันทึกเป็น check-line.php ใน public folder:

<?php
// check-line.php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://yourdomain.com/api/line/status?key=secret123');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// Log to file
$log = date('Y-m-d H:i:s') . " - " . ($data['api_status'] ?? 'unknown') . "\n";
file_put_contents('line-status.log', $log, FILE_APPEND);

// Send email if failed
if (isset($data['api_status']) && $data['api_status'] !== 'success') {
    mail('admin@example.com', 'LINE API Alert', 'LINE API Status: ' . $data['api_status']);
}
?>

# ใช้กับ cPanel cron job:
# */30 * * * * php /home/username/public_html/check-line.php
