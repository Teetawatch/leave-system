import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../config/app_theme.dart';
import '../providers/auth_provider.dart';
import '../providers/leave_provider.dart';
import 'leave_request_screen.dart';
import 'guard_change_request_screen.dart';
import 'leave_history_screen.dart';
import 'notification_screen.dart';
import 'employee_directory_screen.dart';
import '../providers/news_provider.dart';
import '../models/news_model.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/guard_change_provider.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _fadeController;

  @override
  void initState() {
    super.initState();
    _fadeController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 800),
    )..forward();

    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadData();
    });
  }

  void _loadData() {
    final leaveProvider = Provider.of<LeaveProvider>(context, listen: false);
    leaveProvider.fetchLeaveBalances();
    leaveProvider.fetchMyRequests();

    final user = Provider.of<AuthProvider>(context, listen: false).user;
    if (user != null &&
        (user.role == 'supervisor' ||
            user.role == 'manager' ||
            user.role == 'admin')) {
      leaveProvider.fetchPendingApprovals();
    }

    Provider.of<NewsProvider>(context, listen: false).fetchLatestNews();
    Provider.of<GuardChangeProvider>(context, listen: false).fetchMyRequests();
  }

  @override
  void dispose() {
    _fadeController.dispose();
    super.dispose();
  }

  Future<void> _refreshData() async {
    HapticFeedback.lightImpact();
    _loadData();
    await Future.delayed(const Duration(milliseconds: 1000));
  }

  @override
  Widget build(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).user;
    final leaveProvider = Provider.of<LeaveProvider>(context);
    final guardProvider = Provider.of<GuardChangeProvider>(context);
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: isDark
          ? SystemUiOverlayStyle.light
          : SystemUiOverlayStyle.dark.copyWith(
              statusBarColor: Colors.transparent,
            ),
      child: Scaffold(
        backgroundColor: Theme.of(context).scaffoldBackgroundColor,
        body: FadeTransition(
          opacity: _fadeController,
          child: RefreshIndicator(
            onRefresh: _refreshData,
            color: AppTheme.primary,
            child: CustomScrollView(
              physics: const BouncingScrollPhysics(),
              slivers: [
                SliverAppBar(
                  backgroundColor: Colors.transparent,
                  elevation: 0,
                  scrolledUnderElevation: 0,
                  pinned: false,
                  floating: true,
                  snap: true,
                  stretch: true,
                  expandedHeight: 140,
                  collapsedHeight: 140,
                  automaticallyImplyLeading: false,
                  flexibleSpace: FlexibleSpaceBar(
                    background: Padding(
                      padding: EdgeInsets.fromLTRB(
                        24,
                        MediaQuery.of(context).padding.top + 24,
                        24,
                        24,
                      ),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          // Left: Avatar + Heading
                          Row(
                            children: [
                              _buildAvatar(user),
                              const SizedBox(width: 16),
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      Text(
                                        'สวัสดีตอนเย็น',
                                        style: GoogleFonts.kanit(
                                          fontSize: 16,
                                          color: AppTheme.textSub,
                                          fontWeight: FontWeight.w500,
                                        ),
                                      ),
                                      const SizedBox(width: 4),
                                      const Text(
                                        '🌙',
                                        style: TextStyle(fontSize: 16),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 2),
                                  SizedBox(
                                    width:
                                        MediaQuery.of(context).size.width * 0.5,
                                    child: Text(
                                      user?.fullName ?? 'พนักงาน',
                                      style: GoogleFonts.kanit(
                                        fontSize: 17,
                                        fontWeight: FontWeight.w700,
                                        color: isDark
                                            ? Colors.white
                                            : AppTheme.textMain,
                                      ),
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),

                          // Right: Notification Bell
                          _buildNotificationBell(context),
                        ],
                      ),
                    ),
                  ),
                ),

                // 2. Main Stats Card (Gradient)
                SliverPadding(
                  padding: const EdgeInsets.symmetric(horizontal: 24),
                  sliver: SliverToBoxAdapter(
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 32),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFF6C63FF), Color(0xFF8B5CF6)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(32),
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFF6C63FF).withOpacity(0.4),
                            blurRadius: 24,
                            offset: const Offset(0, 12),
                          ),
                        ],
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          _buildStatItem(
                            icon: Icons.calendar_today_rounded,
                            value: _getTotalRemainingDays(leaveProvider),
                            label: 'วันลาคงเหลือ',
                          ),
                          _buildDivider(),
                          _buildStatItem(
                            icon: Icons.pending_actions_rounded,
                            value: leaveProvider.myRequests
                                .where((r) => r.status == 'pending')
                                .length
                                .toString(),
                            label: 'รออนุมัติ',
                          ),
                          _buildDivider(),
                          _buildStatItem(
                            icon: Icons.check_circle_outline_rounded,
                            value: leaveProvider.myRequests
                                .where((r) => r.status == 'approved')
                                .length
                                .toString(),
                            label: 'อนุมัติแล้ว',
                          ),
                        ],
                      ),
                    ),
                  ),
                ),

                // 3. Quick Menu Header
                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(28, 32, 24, 16),
                  sliver: SliverToBoxAdapter(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const Icon(
                              Icons.bolt_rounded,
                              color: AppTheme.warning,
                              size: 24,
                            ),
                            const SizedBox(width: 8),
                            Text(
                              'เมนูด่วน',
                              style: GoogleFonts.kanit(
                                fontSize: 22,
                                fontWeight: FontWeight.w700,
                                color: isDark
                                    ? Colors.white
                                    : AppTheme.textMain,
                              ),
                            ),
                          ],
                        ),
                        Padding(
                          padding: const EdgeInsets.only(left: 32),
                          child: Text(
                            'เข้าถึงฟีเจอร์ที่ใช้บ่อย',
                            style: GoogleFonts.sarabun(
                              fontSize: 14,
                              color: AppTheme.textSub,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),

                // 4. Quick Menu Grid
                SliverPadding(
                  padding: const EdgeInsets.symmetric(horizontal: 24),
                  sliver: SliverGrid.count(
                    crossAxisCount: 2,
                    mainAxisSpacing: 16,
                    crossAxisSpacing: 16,
                    childAspectRatio: 1.1,
                    children: [
                      // Leave Request
                      _buildMenuCard(
                        context,
                        title: 'ลางาน',
                        subtitle: 'เขียนใบลา',
                        icon: Icons.edit_calendar_rounded,
                        color: AppTheme.primary,
                        onTap: () => Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => const LeaveRequestScreen(),
                          ),
                        ),
                      ),
                      // History
                      _buildMenuCard(
                        context,
                        title: 'ประวัติ',
                        subtitle: 'ดูการลาย้อนหลัง',
                        icon: Icons.history_rounded,
                        color: AppTheme.secondary,
                        onTap: () => Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => const ActivityScreen(),
                          ),
                        ),
                      ),
                      // Shift Change
                      _buildMenuCard(
                        context,
                        title: 'เปลี่ยนยาม',
                        subtitle: 'ส่งคำขอใหม่',
                        icon: Icons.swap_horiz_rounded,
                        color: AppTheme.accent,
                        onTap: () => Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => const GuardChangeRequestScreen(),
                          ),
                        ),
                      ),
                      // List/Approvals (Or simple List)
                      _buildMenuCard(
                        context,
                        title: 'รายการ',
                        subtitle: 'ประวัติการเปลี่ยน',
                        icon: Icons.assignment_outlined,
                        color: AppTheme.info,
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => const ActivityScreen(),
                            ),
                          );
                        },
                      ),
                    ],
                  ),
                ),

                // 4.1 Today's Duty Section (New)
                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(24, 24, 24, 0),
                  sliver: SliverToBoxAdapter(
                    child: _buildDutyStatusCard(context),
                  ),
                ),

                // 4.2 Recent Activity Section (New)
                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(24, 24, 24, 8),
                  sliver: SliverToBoxAdapter(
                    child: _buildRecentActivitySection(
                      context,
                      leaveProvider,
                      guardProvider,
                    ),
                  ),
                ),

                // 5. Contact HR (Phonebook)
                SliverPadding(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 24,
                    vertical: 16,
                  ),
                  sliver: SliverToBoxAdapter(
                    child: _buildContactHRCard(context),
                  ),
                ),

                // 6. News Section
                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(24, 0, 24, 100),
                  sliver: SliverToBoxAdapter(child: _buildNewsSection(context)),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // --- Widgets ---

  Widget _buildAvatar(dynamic user) {
    return Container(
      padding: const EdgeInsets.all(3),
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(color: AppTheme.primary.withOpacity(0.5), width: 2),
      ),
      child: CircleAvatar(
        radius: 26,
        backgroundColor: Colors.white,
        child: Text(
          (user?.name ?? 'S')[0].toUpperCase(),
          style: GoogleFonts.kanit(
            fontSize: 24,
            fontWeight: FontWeight.w700,
            color: const Color(0xFF6C63FF), // Match primary purple explicitly
          ),
        ),
      ),
    );
  }

  Widget _buildNotificationBell(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        shape: BoxShape.circle,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: IconButton(
        icon: const Icon(
          Icons.notifications_outlined,
          color: AppTheme.textMain,
        ),
        onPressed: () => Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => const NotificationScreen()),
        ),
      ),
    );
  }

  Widget _buildStatItem({
    required IconData icon,
    required String value,
    required String label,
  }) {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.2),
            shape: BoxShape.circle,
          ),
          child: Icon(icon, color: Colors.white, size: 20),
        ),
        const SizedBox(height: 12),
        Text(
          value,
          style: GoogleFonts.kanit(
            fontSize: 28,
            fontWeight: FontWeight.w700,
            color: Colors.white,
          ),
        ),
        Text(
          label,
          style: GoogleFonts.sarabun(
            fontSize: 12,
            color: Colors.white.withOpacity(0.8),
            fontWeight: FontWeight.w500,
          ),
        ),
      ],
    );
  }

  Widget _buildDivider() {
    return Container(
      width: 1,
      height: 40,
      color: Colors.white.withOpacity(0.2),
    );
  }

  Widget _buildMenuCard(
    BuildContext context, {
    required String title,
    required String subtitle,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
  }) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Container(
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF1E293B) : Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 16,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(24),
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: color.withOpacity(0.15),
                    shape: BoxShape.circle,
                  ),
                  child: Icon(icon, color: color, size: 28),
                ),
                const Spacer(),
                Text(
                  title,
                  style: GoogleFonts.kanit(
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: isDark ? Colors.white : AppTheme.textMain,
                  ),
                ),
                Text(
                  subtitle,
                  style: GoogleFonts.sarabun(
                    fontSize: 13,
                    color: AppTheme.textSub,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildContactHRCard(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => const EmployeeDirectoryScreen(),
          ),
        );
      },
      child: Container(
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: isDark ? const Color(0xFF1E293B) : Colors.white,
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.03),
              blurRadius: 16,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppTheme.primary.withOpacity(0.15),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                Icons.perm_contact_calendar_rounded,
                color: AppTheme.primary,
                size: 30,
              ),
            ),
            const SizedBox(width: 20),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'สมุดโทรศัพท์',
                  style: GoogleFonts.kanit(
                    fontSize: 20,
                    fontWeight: FontWeight.w700,
                    color: isDark ? Colors.white : AppTheme.textMain,
                  ),
                ),
                Text(
                  'ค้นหาเบอร์โทรศัพท์บุคลากร',
                  style: GoogleFonts.sarabun(
                    fontSize: 14,
                    color: AppTheme.textSub,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildNewsSection(BuildContext context) {
    final newsProvider = Provider.of<NewsProvider>(context);
    final isDark = Theme.of(context).brightness == Brightness.dark;

    // Show even if empty or loading to keep UI consistent, or hide if preferred
    if (newsProvider.newsList.isEmpty) return const SizedBox.shrink();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: AppTheme.primary.withOpacity(0.1),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.campaign_rounded,
                    color: AppTheme.primary,
                    size: 20,
                  ),
                ),
                const SizedBox(width: 12),
                Text(
                  'ข่าวสารประชาสัมพันธ์',
                  style: GoogleFonts.kanit(
                    fontSize: 20,
                    fontWeight: FontWeight.w700,
                    color: isDark ? Colors.white : AppTheme.textMain,
                  ),
                ),
              ],
            ),
            // Optional: 'View All' button if needed
            // TextButton(onPressed: (){}, child: Text('ดูทั้งหมด'))
          ],
        ),
        const SizedBox(height: 16),
        GridView.builder(
          padding: EdgeInsets.zero,
          physics: const NeverScrollableScrollPhysics(),
          shrinkWrap: true,
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            mainAxisSpacing: 16,
            crossAxisSpacing: 16,
            childAspectRatio: 0.75, // Taller card to fit content
          ),
          itemCount: newsProvider.newsList.length > 6
              ? 6
              : newsProvider.newsList.length, // Limit to 6
          itemBuilder: (context, index) {
            final news = newsProvider.newsList[index];
            return _buildNewsCard(context, news);
          },
        ),
      ],
    );
  }

  Widget _buildNewsCard(BuildContext context, NewsModel news) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return GestureDetector(
      onTap: () async {
        if (news.attachmentUrl != null) {
          final uri = Uri.parse(news.attachmentUrl!);
          if (await canLaunchUrl(uri)) {
            await launchUrl(uri);
          }
        }
      },
      child: Container(
        // Remove fixed width/margin as GridView handles it
        decoration: BoxDecoration(
          color: isDark ? const Color(0xFF1E293B) : Colors.white,
          borderRadius: BorderRadius.circular(20),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Image Section
              Expanded(
                flex: 5,
                child: Stack(
                  fit: StackFit.expand,
                  children: [
                    Container(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          colors: [
                            AppTheme.primary.withOpacity(0.8),
                            AppTheme.primaryDark,
                          ],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                      ),
                      child: Center(
                        child: Icon(
                          Icons.article_rounded,
                          color: Colors.white.withOpacity(0.5),
                          size: 32,
                        ),
                      ),
                    ),
                    if (news.imageUrl != null)
                      Image.network(
                        news.imageUrl!,
                        fit: BoxFit.cover,
                        errorBuilder: (_, __, ___) => const SizedBox.shrink(),
                      ),

                    // Badge "New"
                    if (DateTime.now()
                            .difference(DateTime.parse(news.createdAt))
                            .inDays <
                        3)
                      Positioned(
                        top: 8,
                        right: 8,
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: AppTheme.error,
                            borderRadius: BorderRadius.circular(12),
                            boxShadow: [
                              BoxShadow(
                                color: AppTheme.error.withOpacity(0.4),
                                blurRadius: 4,
                              ),
                            ],
                          ),
                          child: Text(
                            'NEW',
                            style: GoogleFonts.kanit(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ),
                        ),
                      ),
                  ],
                ),
              ),

              // Content Section
              Expanded(
                flex: 4,
                child: Container(
                  padding: const EdgeInsets.all(12),
                  width: double.infinity,
                  color: isDark ? const Color(0xFF1E293B) : Colors.white,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        news.title,
                        style: GoogleFonts.kanit(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: isDark ? Colors.white : AppTheme.textMain,
                          height: 1.2,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 6),
                      Expanded(
                        child: Text(
                          news.content,
                          style: GoogleFonts.sarabun(
                            fontSize: 11,
                            color: AppTheme.textSub,
                            height: 1.3,
                          ),
                          maxLines: 3,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        DateFormat(
                          'dd MMM yyyy',
                        ).format(DateTime.parse(news.createdAt)),
                        style: GoogleFonts.sarabun(
                          fontSize: 10,
                          color: AppTheme.textSub.withOpacity(0.7),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDutyStatusCard(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    // Mock logic for duty status - in real app, check schedule API
    final isHoliday =
        DateTime.now().weekday == DateTime.saturday ||
        DateTime.now().weekday == DateTime.sunday;
    final statusText = isHoliday ? 'วันหยุด' : 'เข้าเวรปกติ (กำลังพัฒนา)';
    final statusIcon = isHoliday
        ? Icons.weekend_rounded
        : Icons.shield_moon_rounded;
    final statusColor = isHoliday ? AppTheme.success : AppTheme.primary;

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF1E293B) : Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: statusColor.withOpacity(0.3), width: 1),
        boxShadow: [
          BoxShadow(
            color: statusColor.withOpacity(0.1),
            blurRadius: 16,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(statusIcon, color: statusColor, size: 28),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'สถานะเวรยามวันนี้',
                  style: GoogleFonts.sarabun(
                    fontSize: 14,
                    color: AppTheme.textSub,
                  ),
                ),
                Text(
                  statusText,
                  style: GoogleFonts.kanit(
                    fontSize: 18,
                    fontWeight: FontWeight.w600,
                    color: isDark ? Colors.white : AppTheme.textMain,
                  ),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              DateFormat('d MMM').format(DateTime.now()),
              style: GoogleFonts.kanit(
                fontSize: 12,
                fontWeight: FontWeight.bold,
                color: statusColor,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRecentActivitySection(
    BuildContext context,
    LeaveProvider leaveProvider,
    GuardChangeProvider guardProvider,
  ) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    // Combine and sort activities
    final leaveRequests = leaveProvider.myRequests.map(
      (r) => {
        'type': 'leave',
        'title': r.leaveType.name, // Access name property of LeaveType
        'status': r.status,
        'date': r.createdAt, // Already DateTime
        'data': r,
      },
    );

    final guardRequests = guardProvider.myRequests.map(
      (r) => {
        'type': 'guard',
        'title': 'ขอเปลี่ยนเวร',
        'status': r.status,
        'date': DateTime.parse(r.createdAt), // Convert String to DateTime
        'data': r,
      },
    );

    final allActivities = [...leaveRequests, ...guardRequests].toList();
    allActivities.sort((a, b) {
      DateTime da = a['date'] as DateTime;
      DateTime db = b['date'] as DateTime;
      return db.compareTo(da); // Newest first
    });

    if (allActivities.isEmpty) return const SizedBox.shrink();

    // Show only top 1 most recent item to keep it compact, or list a few
    final recent = allActivities.first;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              'รายการล่าสุด',
              style: GoogleFonts.kanit(
                fontSize: 20,
                fontWeight: FontWeight.w700,
                color: isDark ? Colors.white : AppTheme.textMain,
              ),
            ),
            TextButton(
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const ActivityScreen()),
                );
              },
              child: Text(
                'ดูทั้งหมด',
                style: GoogleFonts.kanit(fontSize: 14, color: AppTheme.primary),
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Container(
          width: double.infinity,
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: isDark ? const Color(0xFF1E293B) : Colors.white,
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.03),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color:
                      (recent['type'] == 'leave'
                              ? AppTheme.primary
                              : AppTheme.accent)
                          .withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  recent['type'] == 'leave'
                      ? Icons.event_note_rounded
                      : Icons.swap_horiz_rounded,
                  color: recent['type'] == 'leave'
                      ? AppTheme.primary
                      : AppTheme.accent,
                  size: 24,
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      recent['title'] as String,
                      style: GoogleFonts.kanit(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: isDark ? Colors.white : AppTheme.textMain,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      DateFormat(
                        'd MMM yyyy, HH:mm',
                      ).format(recent['date'] as DateTime),
                      style: GoogleFonts.sarabun(
                        fontSize: 12,
                        color: AppTheme.textSub,
                      ),
                    ),
                  ],
                ),
              ),
              _buildStatusBadge(recent['status'] as String),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildStatusBadge(String status) {
    Color color;
    String label;

    switch (status) {
      case 'approved':
        color = AppTheme.success;
        label = 'อนุมัติ';
        break;
      case 'rejected':
      case 'cancelled':
        color = AppTheme.error;
        label = 'ไม่อนุมัติ';
        break;
      case 'pending':
      default:
        color = AppTheme.warning;
        label = 'รออนุมัติ';
        break;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withOpacity(0.2)),
      ),
      child: Text(
        label,
        style: GoogleFonts.kanit(
          fontSize: 12,
          color: color,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  String _getTotalRemainingDays(LeaveProvider provider) {
    if (provider.balances.isEmpty) return '0';
    double total = 0;
    for (var bal in provider.balances) {
      total += bal.remainingDays;
    }
    return total.toStringAsFixed(0);
  }
}
