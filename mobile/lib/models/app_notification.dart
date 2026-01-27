class AppNotification {
  final String id;
  final String title;
  final String body;
  final DateTime timestamp;
  bool isRead;
  final String type; // 'info', 'success', 'warning', 'error'

  AppNotification({
    required this.id,
    required this.title,
    required this.body,
    required this.timestamp,
    this.isRead = false,
    this.type = 'info',
  });

  factory AppNotification.fromJson(Map<String, dynamic> json) {
    // Determine type based on data["type"] or similar if available, otherwise default
    String type = 'info';
    final data = json['data'] ?? json;

    if (data['type'] != null) {
      type = data['type'];
    }

    return AppNotification(
      id: (json['id'] ?? '').toString(),
      title: data['title'] ?? 'การแจ้งเตือน',
      body: data['body'] ?? data['message'] ?? '',
      timestamp: json['created_at'] != null
          ? DateTime.parse(json['created_at'])
          : DateTime.now(),
      isRead: json['read_at'] != null,
      type: type,
    );
  }
}
