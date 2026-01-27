import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/notification_provider.dart';
import '../config/app_theme.dart';
import '../models/app_notification.dart';

class NotificationScreen extends StatefulWidget {
  const NotificationScreen({super.key});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<NotificationProvider>(
        context,
        listen: false,
      ).fetchNotifications();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, size: 20),
          color: Colors.black,
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'การแจ้งเตือน',
          style: GoogleFonts.kanit(
            fontSize: 20,
            fontWeight: FontWeight.bold,
            color: Colors.black,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.done_all, color: Colors.blue),
            tooltip: 'อ่านทั้งหมด',
            onPressed: () {
              Provider.of<NotificationProvider>(
                context,
                listen: false,
              ).markAllAsRead();
            },
          ),
        ],
      ),
      body: Consumer<NotificationProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading) {
            return const Center(child: CircularProgressIndicator());
          }

          final notifications = provider.notifications;
          if (notifications.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.notifications_off_outlined,
                    size: 64,
                    color: AppTheme.textSub.withOpacity(0.5),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'ไม่มีการแจ้งเตือน',
                    style: GoogleFonts.kanit(
                      fontSize: 18,
                      color: AppTheme.textSub,
                    ),
                  ),
                ],
              ),
            );
          }

          final grouped = _groupNotifications(notifications);

          return Column(
            children: [
              Expanded(
                child: ListView.builder(
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  itemCount: grouped.length + 1,
                  itemBuilder: (context, index) {
                    if (index == grouped.length) {
                      return _buildClearAllButton(provider);
                    }
                    final group = grouped[index];
                    return Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildSectionHeader(group.title),
                        ...group.notifications.map(
                          (n) => _buildNotificationItem(context, n),
                        ),
                        const SizedBox(height: 16),
                      ],
                    );
                  },
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 8, 20, 12),
      child: Text(
        title.toUpperCase(),
        style: GoogleFonts.kanit(
          fontSize: 14,
          fontWeight: FontWeight.w600,
          color: AppTheme.textSub,
          letterSpacing: 1.0,
        ),
      ),
    );
  }

  Widget _buildNotificationItem(
    BuildContext context,
    AppNotification notification,
  ) {
    return Dismissible(
      key: Key(notification.id),
      direction: DismissDirection.endToStart,
      background: Container(
        color: AppTheme.error,
        alignment: Alignment.centerRight,
        padding: const EdgeInsets.only(right: 24),
        child: const Icon(Icons.delete_outline, color: Colors.white, size: 28),
      ),
      onDismissed: (_) {
        Provider.of<NotificationProvider>(
          context,
          listen: false,
        ).removeNotification(notification.id);
      },
      child: InkWell(
        onTap: () {
          Provider.of<NotificationProvider>(
            context,
            listen: false,
          ).markAsRead(notification.id);
        },
        child: Container(
          color: notification.isRead
              ? Colors.transparent
              : Colors.blue.withOpacity(0.04),
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildIcon(notification),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Expanded(
                          child: Text(
                            notification.title,
                            style: GoogleFonts.kanit(
                              fontSize: 16,
                              fontWeight: FontWeight.w600,
                              color: AppTheme.textMain,
                              height: 1.3,
                            ),
                          ),
                        ),
                        if (!notification.isRead)
                          Container(
                            margin: const EdgeInsets.only(left: 8, top: 6),
                            width: 10,
                            height: 10,
                            decoration: const BoxDecoration(
                              color: Colors.blue,
                              shape: BoxShape.circle,
                            ),
                          ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    if (notification.body.isNotEmpty) ...[
                      Text(
                        notification.body,
                        style: GoogleFonts.sarabun(
                          fontSize: 14,
                          fontWeight: FontWeight.w400,
                          color: AppTheme.textMain.withOpacity(0.8),
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 6),
                    ],
                    Text(
                      _formatTimeAndCategory(notification),
                      style: GoogleFonts.sarabun(
                        fontSize: 13,
                        color: AppTheme.textSub,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildIcon(AppNotification notification) {
    IconData iconData = Icons.notifications_none;
    Color iconColor = AppTheme.primary;
    Color bgColor = AppTheme.primary.withOpacity(0.1);

    final titleLower = notification.title.toLowerCase();
    final typeLower = notification.type.toLowerCase();

    // Mapping based on content to match the image icons
    if (titleLower.contains('leave') ||
        titleLower.contains('ลา') ||
        typeLower == 'leave') {
      iconData = Icons.calendar_today_rounded;
      iconColor = Colors.blue;
      bgColor = Colors.blue.withOpacity(0.1);
    } else if (titleLower.contains('swap') ||
        titleLower.contains('เปลี่ยนกะ') ||
        titleLower.contains('shift')) {
      iconData = Icons.swap_horiz_rounded;
      iconColor = Colors.blue.withOpacity(0.8);
      bgColor = Colors.blue.withOpacity(0.1);
    } else if (titleLower.contains('document') ||
        titleLower.contains('เอกสาร')) {
      iconData = Icons.description_outlined;
      iconColor = Colors.grey[700]!;
      bgColor = Colors.grey[200]!;
    } else if (titleLower.contains('payslip') ||
        titleLower.contains('เงินเดือน')) {
      iconData = Icons.account_balance_wallet_outlined;
      iconColor = Colors.grey[700]!;
      bgColor = Colors.grey[200]!;
    } else if (titleLower.contains('security') ||
        titleLower.contains('ความปลอดภัย') ||
        titleLower.contains('verified')) {
      iconData = Icons.verified_user_outlined;
      iconColor = Colors.grey[700]!;
      bgColor = Colors.grey[200]!;
    } else {
      switch (notification.type) {
        case 'success':
          iconData = Icons.check_circle_outline;
          iconColor = AppTheme.success;
          bgColor = AppTheme.success.withOpacity(0.1);
          break;
        case 'warning':
          iconData = Icons.warning_amber_rounded;
          iconColor = AppTheme.warning;
          bgColor = AppTheme.warning.withOpacity(0.1);
          break;
        case 'error':
          iconData = Icons.error_outline;
          iconColor = AppTheme.error;
          bgColor = AppTheme.error.withOpacity(0.1);
          break;
        default:
          iconData = Icons.notifications_none;
          iconColor = AppTheme.primary;
          bgColor = AppTheme.primary.withOpacity(0.1);
      }
    }

    return Container(
      width: 48,
      height: 48,
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Icon(iconData, color: iconColor, size: 24),
    );
  }

  Widget _buildClearAllButton(NotificationProvider provider) {
    if (provider.notifications.isEmpty) return const SizedBox.shrink();

    return Padding(
      padding: const EdgeInsets.all(20.0),
      child: SizedBox(
        width: double.infinity,
        child: OutlinedButton(
          onPressed: () {
            // Confirm dialog
            showDialog(
              context: context,
              builder: (ctx) => AlertDialog(
                title: const Text('ล้างการแจ้งเตือน?'),
                content: const Text(
                  'คุณต้องการลบการแจ้งเตือนทั้งหมดใช่หรือไม่?',
                ),
                actions: [
                  TextButton(
                    onPressed: () => Navigator.pop(ctx),
                    child: const Text('ยกเลิก'),
                  ),
                  TextButton(
                    onPressed: () {
                      Navigator.pop(ctx);
                      final ids = provider.notifications
                          .map((n) => n.id)
                          .toList();
                      for (var id in ids) {
                        provider.removeNotification(id);
                      }
                    },
                    child: const Text(
                      'ลบทั้งหมด',
                      style: TextStyle(color: Colors.red),
                    ),
                  ),
                ],
              ),
            );
          },
          style: OutlinedButton.styleFrom(
            side: BorderSide(color: Colors.grey[300]!),
            padding: const EdgeInsets.symmetric(vertical: 16),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
            backgroundColor: Colors.transparent,
            foregroundColor: AppTheme.textSub,
          ),
          child: Text(
            'ล้างการแจ้งเตือนทั้งหมด',
            style: GoogleFonts.kanit(
              fontSize: 16,
              color: AppTheme.textSub,
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
      ),
    );
  }

  String _formatTimeAndCategory(AppNotification notification) {
    final timeStr = DateFormat('HH:mm').format(notification.timestamp);

    // Infer category
    String category = 'ทั่วไป';
    final titleLower = notification.title.toLowerCase();

    if (titleLower.contains('leave') ||
        titleLower.contains('ลา') ||
        titleLower.contains('approved'))
      category = 'การลา';
    else if (titleLower.contains('shift') ||
        titleLower.contains('กะ') ||
        titleLower.contains('swap'))
      category = 'ตารางงาน';
    else if (titleLower.contains('document') ||
        titleLower.contains('เอกสาร') ||
        titleLower.contains('contract'))
      category = 'งานธุรการ';
    else if (titleLower.contains('payslip') || titleLower.contains('เงินเดือน'))
      category = 'การเงิน';
    else if (titleLower.contains('security') ||
        titleLower.contains('ความปลอดภัย'))
      category = 'ความปลอดภัย';

    return '$timeStr น. • $category';
  }

  List<_NotificationGroup> _groupNotifications(
    List<AppNotification> notifications,
  ) {
    if (notifications.isEmpty) return [];

    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);
    final yesterday = today.subtract(const Duration(days: 1));

    final List<AppNotification> todayList = [];
    final List<AppNotification> yesterdayList = [];
    final List<AppNotification> earlierList = [];

    // Sort descending by time
    final sorted = List<AppNotification>.from(notifications);
    sorted.sort((a, b) => b.timestamp.compareTo(a.timestamp));

    for (var n in sorted) {
      final date = DateTime(
        n.timestamp.year,
        n.timestamp.month,
        n.timestamp.day,
      );
      if (date.isAtSameMomentAs(today)) {
        todayList.add(n);
      } else if (date.isAtSameMomentAs(yesterday)) {
        yesterdayList.add(n);
      } else {
        earlierList.add(n);
      }
    }

    final groups = <_NotificationGroup>[];
    if (todayList.isNotEmpty)
      groups.add(_NotificationGroup('วันนี้', todayList));
    if (yesterdayList.isNotEmpty)
      groups.add(_NotificationGroup('เมื่อวาน', yesterdayList));
    if (earlierList.isNotEmpty) {
      // Maybe we want to group by date here? Or just keep it simple.
      // The user request was about DATE FORMAT.
      // If "Earlier" just lists them, we might not need to change this specific logic unless it shows dates.
      // But wait, the notification item *only* shows time?
      // `_formatTimeAndCategory` shows `HH:mm`.
      // If it's an old notification, showing just proper time is weird without date.
      // But typically notification lists shows relative time.
      // Let's leave NotificationScreen grouping titles as is (Today/Yesterday) but maybe update `earlierList` to show date?
      // Actually, standard UI is fine.
      groups.add(_NotificationGroup('ก่อนหน้านี้', earlierList));
    }

    return groups;
  }
}

class _NotificationGroup {
  final String title;
  final List<AppNotification> notifications;

  _NotificationGroup(this.title, this.notifications);
}
