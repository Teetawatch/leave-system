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

class _NotificationScreenState extends State<NotificationScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<NotificationProvider>(
        context,
        listen: false,
      ).fetchNotifications();
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text(
          'การแจ้งเตือน',
          style: GoogleFonts.kanit(
            fontSize: 20,
            fontWeight: FontWeight.bold,
            color: AppTheme.textMain,
          ),
        ),
        centerTitle: true,
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, size: 20),
          color: AppTheme.textMain,
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.done_all_rounded, color: AppTheme.primary),
            tooltip: 'อ่านทั้งหมด',
            onPressed: () {
              Provider.of<NotificationProvider>(
                context,
                listen: false,
              ).markAllAsRead();
            },
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppTheme.primary,
          unselectedLabelColor: AppTheme.textSub,
          labelStyle: GoogleFonts.kanit(
            fontSize: 14,
            fontWeight: FontWeight.bold,
          ),
          unselectedLabelStyle: GoogleFonts.kanit(fontSize: 14),
          indicatorColor: AppTheme.primary,
          indicatorWeight: 3,
          indicatorSize: TabBarIndicatorSize.label,
          tabs: const [
            Tab(text: 'ทั้งหมด'),
            Tab(text: 'การอนุมัติ'),
            Tab(text: 'ข่าวสารองค์กร'),
          ],
        ),
      ),
      body: Consumer<NotificationProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading) {
            return const Center(child: CircularProgressIndicator());
          }

          if (provider.notifications.isEmpty) {
            return _buildEmptyState();
          }

          final allNotifications = provider.notifications;
          final approvalNotifications = allNotifications.where((n) {
            final titleLower = n.title.toLowerCase();
            final bodyLower = n.body.toLowerCase();
            return titleLower.contains('approved') ||
                titleLower.contains('rejected') ||
                titleLower.contains('อนุมัติ') ||
                titleLower.contains('ปฏิเสธ') ||
                titleLower.contains('leave') ||
                titleLower.contains('ลา') ||
                titleLower.contains('เวร') ||
                titleLower.contains('guard') ||
                titleLower.contains('swap') ||
                bodyLower.contains('อนุมัติ') ||
                bodyLower.contains('ปฏิเสธ') ||
                bodyLower.contains('เปลี่ยนเวร');
          }).toList();

          final newsNotifications = allNotifications.where((n) {
            final titleLower = n.title.toLowerCase();
            final bodyLower = n.body.toLowerCase();
            final isApproval =
                titleLower.contains('approved') ||
                titleLower.contains('rejected') ||
                titleLower.contains('อนุมัติ') ||
                titleLower.contains('ปฏิเสธ') ||
                titleLower.contains('leave') ||
                titleLower.contains('ลา') ||
                titleLower.contains('เวร') ||
                titleLower.contains('guard') ||
                titleLower.contains('swap') ||
                bodyLower.contains('อนุมัติ') ||
                bodyLower.contains('ปฏิเสธ') ||
                bodyLower.contains('เปลี่ยนเวร');
            return !isApproval;
          }).toList();

          return TabBarView(
            controller: _tabController,
            children: [
              _buildNotificationList(context, allNotifications, provider),
              _buildNotificationList(
                context,
                approvalNotifications,
                provider,
                emptyMessage: 'ไม่มีรายการอนุมัติ',
              ),
              _buildNotificationList(
                context,
                newsNotifications,
                provider,
                emptyMessage: 'ไม่มีข่าวสารใหม่',
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildEmptyState({String message = 'ไม่มีการแจ้งเตือน'}) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: AppTheme.primary.withOpacity(0.05),
              shape: BoxShape.circle,
            ),
            child: Icon(
              Icons.notifications_off_outlined,
              size: 48,
              color: AppTheme.textSub.withOpacity(0.5),
            ),
          ),
          const SizedBox(height: 16),
          Text(
            message,
            style: GoogleFonts.kanit(
              fontSize: 16,
              color: AppTheme.textSub,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildNotificationList(
    BuildContext context,
    List<AppNotification> notifications,
    NotificationProvider provider, {
    String? emptyMessage,
  }) {
    if (notifications.isEmpty) {
      return _buildEmptyState(message: emptyMessage ?? 'ไม่มีการแจ้งเตือน');
    }

    final grouped = _groupNotifications(notifications);

    return ListView.builder(
      padding: const EdgeInsets.symmetric(vertical: 16),
      itemCount: grouped.length,
      itemBuilder: (context, index) {
        final group = grouped[index];
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildSectionHeader(group.title),
            ...group.notifications.map(
              (n) => _buildNotificationItem(context, n),
            ),
            const SizedBox(height: 12),
          ],
        );
      },
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

          _handleNotificationTap(context, notification);
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
        titleLower.contains('เปลี่ยนเวร') ||
        titleLower.contains('shift') ||
        titleLower.contains('guard') ||
        typeLower == 'guard_change') {
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

  void _handleNotificationTap(
    BuildContext context,
    AppNotification notification,
  ) {
    // Basic navigation logic based on title/content
    final titleLower = notification.title.toLowerCase();

    if (titleLower.contains('leave') ||
        titleLower.contains('ลา') ||
        titleLower.contains('approve') ||
        titleLower.contains('อนุมัติ')) {
      // Navigate to History or relevant screen
      // ideally we would have a specific route or id in notification data
      // For now, we can pop back to main nav (index 1 is history)
      // But simply popping might not be enough if we are deep.
      // Assuming this screen is pushed on top of MainNavigation
      // We can't easily switch the tab of the underlying MainNavigation without context access or provider.
      // But wait, NotificationScreen is likely pushed.
      // Actually, let's just show a bottom sheet with full details for now as a "Smart" interaction
      _showNotificationDetail(context, notification);
    } else {
      _showNotificationDetail(context, notification);
    }
  }

  void _showNotificationDetail(
    BuildContext context,
    AppNotification notification,
  ) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        height: MediaQuery.of(context).size.height * 0.7,
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
        ),
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: Colors.grey[300],
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
            const SizedBox(height: 24),
            Row(
              children: [
                _buildIcon(notification),
                const SizedBox(width: 16),
                Expanded(
                  child: Text(
                    notification.title,
                    style: GoogleFonts.kanit(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      color: AppTheme.textMain,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Text(
              DateFormat(
                'd MMMM yyyy HH:mm',
                'th',
              ).format(notification.timestamp),
              style: GoogleFonts.sarabun(fontSize: 14, color: AppTheme.textSub),
            ),
            const Divider(height: 32),
            Expanded(
              child: SingleChildScrollView(
                child: Text(
                  notification.body,
                  style: GoogleFonts.sarabun(
                    fontSize: 16,
                    color: AppTheme.textMain,
                    height: 1.6,
                  ),
                ),
              ),
            ),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () => Navigator.pop(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primary,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
                child: Text(
                  'ปิด',
                  style: GoogleFonts.kanit(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                  ),
                ),
              ),
            ),
          ],
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
        titleLower.contains('approved')) {
      category = 'การลา';
    } else if (titleLower.contains('shift') ||
        titleLower.contains('กะ') ||
        titleLower.contains('เวร') ||
        titleLower.contains('swap') ||
        titleLower.contains('guard'))
      category = 'การเปลี่ยนเวร';
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
    if (todayList.isNotEmpty) {
      groups.add(_NotificationGroup('วันนี้', todayList));
    }
    if (yesterdayList.isNotEmpty) {
      groups.add(_NotificationGroup('เมื่อวาน', yesterdayList));
    }
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
