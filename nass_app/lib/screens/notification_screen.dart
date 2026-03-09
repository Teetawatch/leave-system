import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../config/api_config.dart';
import '../config/app_theme.dart';
import '../models/app_notification.dart';
import '../services/api_service.dart';

class NotificationScreen extends StatefulWidget {
  const NotificationScreen({super.key});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  final ApiService _api = ApiService();
  List<AppNotification> _notifications = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadNotifications();
  }

  Future<void> _loadNotifications() async {
    setState(() => _isLoading = true);
    try {
      final response = await _api.get(ApiConfig.notifications);
      final data = response['data'];
      if (data is List) {
        _notifications = data.map((n) => AppNotification.fromJson(n)).toList();
      }
    } catch (e) {
      debugPrint('Notifications load error: $e');
    }
    if (mounted) setState(() => _isLoading = false);
  }

  Future<void> _markAsRead(String id) async {
    try {
      await _api.post(ApiConfig.notificationRead(id));
      _loadNotifications();
    } catch (_) {}
  }

  Future<void> _markAllAsRead() async {
    try {
      await _api.post(ApiConfig.notificationReadAll);
      _loadNotifications();
    } catch (_) {}
  }

  Future<void> _deleteNotification(String id) async {
    try {
      await _api.delete(ApiConfig.notificationDelete(id));
      _loadNotifications();
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    final unreadCount = _notifications.where((n) => !n.isRead).length;

    return Scaffold(
      appBar: AppBar(
        title: Text('การแจ้งเตือน', style: AppTheme.heading(18)),
        actions: [
          if (unreadCount > 0)
            TextButton.icon(
              onPressed: _markAllAsRead,
              icon: const Icon(Icons.done_all_rounded, size: 18),
              label: const Text('อ่านทั้งหมด'),
            ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _loadNotifications,
        color: AppTheme.primary,
        child: _isLoading
            ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
            : _notifications.isEmpty
                ? _buildEmpty()
                : ListView.separated(
                    padding: const EdgeInsets.all(16),
                    itemCount: _notifications.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 8),
                    itemBuilder: (_, i) => _buildNotificationItem(_notifications[i]),
                  ),
      ),
    );
  }

  Widget _buildEmpty() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: AppTheme.primary.withValues(alpha: 0.06),
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.notifications_off_outlined, size: 48, color: AppTheme.textMuted.withValues(alpha: 0.5)),
          ),
          const SizedBox(height: 16),
          Text('ไม่มีการแจ้งเตือน', style: AppTheme.heading(16, color: AppTheme.textMuted)),
        ],
      ),
    );
  }

  Widget _buildNotificationItem(AppNotification n) {
    String timeText = '';
    try {
      final dt = DateTime.parse(n.createdAt);
      final now = DateTime.now();
      final diff = now.difference(dt);
      if (diff.inMinutes < 60) {
        timeText = '${diff.inMinutes} นาทีที่แล้ว';
      } else if (diff.inHours < 24) {
        timeText = '${diff.inHours} ชั่วโมงที่แล้ว';
      } else if (diff.inDays < 7) {
        timeText = '${diff.inDays} วันที่แล้ว';
      } else {
        timeText = DateFormat('d MMM yyyy', 'th').format(dt);
      }
    } catch (_) {
      timeText = n.createdAt;
    }

    final isLeave = n.type.contains('Leave');
    final isGuardChange = n.type.contains('GuardChange');

    IconData icon;
    Color iconColor;
    if (n.type.contains('Approved') || n.type.contains('StatusUpdated')) {
      icon = Icons.check_circle_rounded;
      iconColor = AppTheme.success;
    } else if (n.type.contains('Rejected')) {
      icon = Icons.cancel_rounded;
      iconColor = AppTheme.error;
    } else if (isGuardChange) {
      icon = Icons.swap_horiz_rounded;
      iconColor = AppTheme.secondary;
    } else {
      icon = Icons.notifications_rounded;
      iconColor = AppTheme.primary;
    }

    return Dismissible(
      key: Key(n.id),
      direction: DismissDirection.endToStart,
      background: Container(
        alignment: Alignment.centerRight,
        padding: const EdgeInsets.only(right: 20),
        decoration: BoxDecoration(
          color: AppTheme.error.withValues(alpha: 0.1),
          borderRadius: BorderRadius.circular(18),
        ),
        child: const Icon(Icons.delete_outline_rounded, color: AppTheme.error),
      ),
      onDismissed: (_) => _deleteNotification(n.id),
      child: Material(
        color: n.isRead ? AppTheme.surface : AppTheme.primary.withValues(alpha: 0.04),
        borderRadius: BorderRadius.circular(18),
        child: InkWell(
          onTap: () {
            if (!n.isRead) _markAsRead(n.id);
            // Navigate based on type
            if (isLeave && n.requestId != null) {
              Navigator.pushNamed(context, '/leave/detail', arguments: n.requestId);
            } else if (isGuardChange && n.requestId != null) {
              Navigator.pushNamed(context, '/guard-change/detail', arguments: n.requestId);
            }
          },
          borderRadius: BorderRadius.circular(18),
          child: Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(18),
              boxShadow: AppTheme.softShadow,
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color: iconColor.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Icon(icon, color: iconColor, size: 20),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        n.title,
                        style: AppTheme.heading(14, weight: n.isRead ? FontWeight.w500 : FontWeight.w600),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      if (n.message.isNotEmpty) ...[
                        const SizedBox(height: 2),
                        Text(
                          n.message,
                          style: AppTheme.body(13, color: AppTheme.textSecondary),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                      const SizedBox(height: 4),
                      Text(timeText, style: AppTheme.body(11, color: AppTheme.textMuted)),
                    ],
                  ),
                ),
                if (!n.isRead)
                  Container(
                    width: 8,
                    height: 8,
                    decoration: const BoxDecoration(
                      color: AppTheme.primary,
                      shape: BoxShape.circle,
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
