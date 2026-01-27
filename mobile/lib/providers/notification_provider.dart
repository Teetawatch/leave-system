import 'package:flutter/material.dart';
import '../models/app_notification.dart';
import '../services/api_service.dart';
import '../services/notification_service.dart';

class NotificationProvider with ChangeNotifier {
  List<AppNotification> _notifications = [];
  bool _isLoading = false;

  List<AppNotification> get notifications => [..._notifications];
  bool get isLoading => _isLoading;
  int get unreadCount => _notifications.where((n) => !n.isRead).length;

  NotificationProvider() {
    _initRealTimeUpdates();
  }

  void _initRealTimeUpdates() {
    NotificationService().messageStream.listen((message) {
      if (message.notification != null) {
        final notification = AppNotification(
          id: message.messageId ?? DateTime.now().toIso8601String(),
          title: message.notification!.title ?? 'การแจ้งเตือน',
          body: message.notification!.body ?? '',
          timestamp: message.sentTime ?? DateTime.now(),
          isRead: false,
          type: message.data['type'] ?? 'info',
        );
        addNotification(notification);
      }
    });
  }

  Future<void> fetchNotifications() async {
    _isLoading = true;
    notifyListeners();
    try {
      final response = await ApiService().client.get('/notifications');
      debugPrint('Notification Response: ${response.data}'); // Debug print

      List<dynamic> listData = [];
      if (response.data is List) {
        listData = response.data;
      } else if (response.data is Map && response.data['data'] is List) {
        listData = response.data['data'];
      }

      _notifications = listData
          .map((json) => AppNotification.fromJson(json))
          .toList();
    } catch (e) {
      debugPrint('Error fetching notifications: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> markAsRead(String id) async {
    final index = _notifications.indexWhere((n) => n.id == id);
    if (index >= 0) {
      _notifications[index].isRead = true;
      notifyListeners();
      try {
        await ApiService().client.post('/notifications/$id/read');
      } catch (e) {
        debugPrint('Error marking notification as read: $e');
      }
    }
  }

  Future<void> markAllAsRead() async {
    for (var n in _notifications) {
      n.isRead = true;
    }
    notifyListeners();
    try {
      await ApiService().client.post('/notifications/read-all');
    } catch (e) {
      debugPrint('Error marking all notifications as read: $e');
    }
  }

  void removeNotification(String id) {
    _notifications.removeWhere((n) => n.id == id);
    notifyListeners();
    // API call to delete if supported
  }

  void addNotification(AppNotification notification) {
    _notifications.insert(0, notification);
    notifyListeners();
  }
}
