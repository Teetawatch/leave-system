class AppConfig {
  // สำหรับ Android Emulator (AVD) ให้ใช้ 10.0.2.2
  // สำหรับ iOS Simulator ให้ใช้ 127.0.0.1
  // สำหรับเครื่องจริง ให้ใช้ IP ของเครื่องคอมพิวเตอร์คุณ
  static const String baseUrl = 'http://10.0.2.2:8000/api';
  static const String appName =
      'ระบบบริหารจัดการงานธุรการด้านกำลังพล โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ';
}
