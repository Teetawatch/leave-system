import 'package:flutter/material.dart';
import '../models/app_notification.dart';
import '../services/api_service.dart';

class NotificationProvider with ChangeNotifier {
  List<AppNotification> _notifications = [];
  bool _isLoading = false;

  List<AppNotification> get notifications => [..._notifications];
  bool get isLoading => _isLoading;
  int get unreadCount => _notifications.where((n) => !n.isRead).length;

  Future<void> fetchNotifications() async {
    _isLoading = true;
    notifyListeners();
    try {
      final response = await ApiService().client.get('/notifications');
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'];
        _notifications = data
            .map((json) => AppNotification.fromJson(json))
            .toList();
      }
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
