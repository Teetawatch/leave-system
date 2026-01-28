import 'package:flutter/foundation.dart'; // ต้อง import ตัวนี้ก่อน

class AppConfig {
  // Configs
  //static const String _localUrl = 'http://10.0.2.2:8000/api'; // สำหรับ Emulator
  // static const String _localUrl = 'http://192.168.1.xxx:8000/api'; // สำหรับเครื่องจริง (Wifi เดียวกัน)

  static const String _productionUrl = 'https://nass.ac.th/leave/index.php/api';

  // Logic: ถ้าเป็น Release Mode (ตอน Build APK) ให้ใช้ลิงก์จริงอัตโนมัติ
  // แต่ถ้า Debug Mode ให้ใช้ลิงก์ Local
  static String get baseUrl {
    if (kReleaseMode) {
      return _productionUrl;
    }
    // อยากใช้อันไหนตอนแก้โค้ด ก็เลือกคืนค่านั้นครับ
    // return _localUrl;
    return _productionUrl; // ตอนนี้คุณอยากเทสกับ Server จริง ก็เปิดบรรทัดนี้ไว้
  }

  static const String appName =
      'ระบบบริหารจัดการงานธุรการด้านกำลังพล โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ';
}
