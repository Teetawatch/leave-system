class ApiConfig {
  // Change this to your actual server URL
  // For local development, use: http://127.0.0.1:8000
  // For production, update to your actual domain
  // Android Emulator ใช้ 10.0.2.2 เพื่อเข้าถึง host machine
  // สำหรับอุปกรณ์จริง ให้ใช้ IP ของเครื่อง เช่น http://192.168.x.x:8000
  static const String baseUrl = 'https://hrmis.nass.ac.th';
  static const String storageUrl = '$baseUrl/storage';
  static const String apiUrl = '$baseUrl/api';

  /// Normalize a storage URL from API — handles cases where Laravel APP_URL
  /// was misconfigured (http vs https, wrong host, relative path, etc.)
  static String? normalizeStorageUrl(String? url) {
    if (url == null || url.isEmpty) return null;
    // Already a full URL — replace host to ensure correct base
    if (url.startsWith('http://') || url.startsWith('https://')) {
      final uri = Uri.tryParse(url);
      if (uri == null) return null;
      // Rebase to our known storageUrl if path contains /storage/
      final idx = uri.path.indexOf('/storage/');
      if (idx >= 0) {
        return '$storageUrl${uri.path.substring(idx + '/storage'.length)}';
      }
      return url;
    }
    // Relative path like "avatars/xxx.jpg"
    return '$storageUrl/$url';
  }

  // Auth
  static const String login = '$apiUrl/login';
  static const String logout = '$apiUrl/logout';
  static const String me = '$apiUrl/me';
  static const String profile = '$apiUrl/profile';
  static const String fcmToken = '$apiUrl/fcm-token';

  // Leave Types
  static const String leaveTypes = '$apiUrl/leave-types';

  // Leave Balance
  static const String leaveBalance = '$apiUrl/leave-balance';

  // Leave Requests
  static const String leaveRequests = '$apiUrl/leave-requests';
  static String leaveRequestDetail(int id) => '$apiUrl/leave-requests/$id';
  static String leaveRequestCancel(int id) => '$apiUrl/leave-requests/$id/cancel';
  static String leaveRequestPdf(int id) => '$apiUrl/leave-requests/$id/pdf';

  // Approvals
  static const String approvals = '$apiUrl/approvals';
  static String approvalApprove(int id) => '$apiUrl/approvals/$id/approve';
  static String approvalReject(int id) => '$apiUrl/approvals/$id/reject';

  // Guard Change Requests
  static const String guardChangeRequests = '$apiUrl/guard-change-requests';
  static const String guardChangeUsers = '$apiUrl/guard-change-requests/users';
  static String guardChangeDetail(int id) => '$apiUrl/guard-change-requests/$id';
  static String guardChangePdf(int id) => '$apiUrl/guard-change-requests/$id/pdf';

  // Guard Change Approvals
  static const String guardChangeApprovals = '$apiUrl/guard-change-approvals';
  static String guardChangeApprove(int id) => '$apiUrl/guard-change-approvals/$id/approve';
  static String guardChangeReject(int id) => '$apiUrl/guard-change-approvals/$id/reject';

  // Reports
  static const String reportLeaveSummary = '$apiUrl/reports/leave-summary';
  static const String reportGuardChange = '$apiUrl/reports/guard-change';

  // Notifications
  static const String notifications = '$apiUrl/notifications';
  static String notificationRead(String id) => '$apiUrl/notifications/$id/read';
  static const String notificationReadAll = '$apiUrl/notifications/read-all';
  static String notificationDelete(String id) => '$apiUrl/notifications/$id';

  // Today's Guard Duty
  static const String todayGuardDuty = '$apiUrl/today-guard-duty';

  // Duty Roster
  static const String dutyRoster = '$apiUrl/duty-roster';
  static const String dutyRosterMonthly = '$apiUrl/duty-roster/monthly';
}
