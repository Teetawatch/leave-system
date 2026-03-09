class AppNotification {
  final String id;
  final String type;
  final Map<String, dynamic> data;
  final String? readAt;
  final String createdAt;

  AppNotification({
    required this.id,
    required this.type,
    required this.data,
    this.readAt,
    required this.createdAt,
  });

  factory AppNotification.fromJson(Map<String, dynamic> json) {
    return AppNotification(
      id: json['id'] ?? '',
      type: json['type'] ?? '',
      data: json['data'] is Map ? Map<String, dynamic>.from(json['data']) : {},
      readAt: json['read_at'],
      createdAt: json['created_at'] ?? '',
    );
  }

  bool get isRead => readAt != null;

  String get title => data['title'] ?? data['message'] ?? 'การแจ้งเตือน';
  String get message => data['body'] ?? data['message'] ?? '';
  int? get requestId => data['request_id'];
}
