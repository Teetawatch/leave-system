import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:url_launcher/url_launcher.dart';
import '../config/api_config.dart';
import '../config/app_theme.dart';
import '../models/leave_balance.dart';
import '../models/leave_request.dart';
import '../providers/auth_provider.dart';
import '../services/api_service.dart';
import '../services/news_service.dart';
import '../services/guard_duty_service.dart';
import '../widgets/status_badge.dart';
import '../widgets/user_avatar.dart';
import 'notification_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final ApiService _api = ApiService();
  List<LeaveBalance> _balances = [];
  List<LeaveRequest> _recentRequests = [];
  List<NewsItem> _news = [];
  List<Map<String, dynamic>> _todayGuards = [];
  int _pendingApprovals = 0;
  int _unreadNotifications = 0;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    try {
      final results = await Future.wait([
        _api.get(ApiConfig.leaveBalance),
        _api.get(ApiConfig.leaveRequests, queryParams: {'per_page': '5'}),
        _api.get(ApiConfig.notifications),
        NewsService.fetchNews(),
      ]);

      // Balances
      final balanceData = results[0] as Map<String, dynamic>;
      final bData = balanceData['data'];
      if (bData != null && bData['balances'] != null) {
        _balances = (bData['balances'] as List)
            .map((b) => LeaveBalance.fromJson(b))
            .toList();
      }

      // Recent requests
      final requestData = (results[1] as Map<String, dynamic>)['data'];
      if (requestData != null) {
        final list = requestData is List ? requestData : [];
        _recentRequests = list.map((r) => LeaveRequest.fromJson(r)).toList();
      }

      // Notifications
      final notifData = (results[2] as Map<String, dynamic>)['data'];
      if (notifData != null && notifData is List) {
        _unreadNotifications = notifData.where((n) => n['read_at'] == null).length;
      }

      // News
      _news = results[3] as List<NewsItem>;

      // Today's guard duty
      _todayGuards = await GuardDutyService.getTodayGuardDuty();

      // Check pending approvals for approvers
      final user = context.read<AuthProvider>().user;
      if (user != null && user.canApprove) {
        try {
          final approvalRes = await _api.get(ApiConfig.approvals);
          _pendingApprovals = approvalRes['meta']?['total'] ?? 0;
        } catch (_) {}
      }
    } catch (e) {
      debugPrint('Dashboard load error: $e');
    }
    if (mounted) setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final now = DateTime.now();
    final thaiDate = DateFormat('EEEE ที่ d MMMM yyyy', 'th').format(now);

    return Scaffold(
      body: RefreshIndicator(
        onRefresh: _loadData,
        color: AppTheme.primary,
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            // Premium Header
            SliverToBoxAdapter(
              child: Container(
                decoration: const BoxDecoration(gradient: AppTheme.primaryGradient),
                child: SafeArea(
                  bottom: false,
                  child: Stack(
                    children: [
                      // Decorative background circles
                      Positioned(
                        right: -30,
                        top: -20,
                        child: Container(
                          width: 160,
                          height: 160,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: Colors.white.withValues(alpha: 0.05),
                          ),
                        ),
                      ),
                      Positioned(
                        right: 40,
                        top: 60,
                        child: Container(
                          width: 80,
                          height: 80,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: Colors.white.withValues(alpha: 0.04),
                          ),
                        ),
                      ),
                      Positioned(
                        left: -40,
                        bottom: 0,
                        child: Container(
                          width: 120,
                          height: 120,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: Colors.white.withValues(alpha: 0.04),
                          ),
                        ),
                      ),
                      // Content
                      Padding(
                        padding: const EdgeInsets.fromLTRB(20, 12, 16, 20),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Top row: notification bell right
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                // Date pill
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                  decoration: BoxDecoration(
                                    color: Colors.white.withValues(alpha: 0.15),
                                    borderRadius: BorderRadius.circular(20),
                                    border: Border.all(color: Colors.white.withValues(alpha: 0.2)),
                                  ),
                                  child: Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Icon(Icons.calendar_today_rounded, size: 12, color: Colors.white.withValues(alpha: 0.9)),
                                      const SizedBox(width: 6),
                                      Text(
                                        thaiDate,
                                        style: GoogleFonts.prompt(
                                          color: Colors.white.withValues(alpha: 0.95),
                                          fontSize: 11,
                                          fontWeight: FontWeight.w500,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                _buildNotificationBell(),
                              ],
                            ),
                            const SizedBox(height: 20),
                            // Avatar + greeting row
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.center,
                              children: [
                                // Avatar with double ring
                                Stack(
                                  alignment: Alignment.center,
                                  children: [
                                    Container(
                                      width: 68,
                                      height: 68,
                                      decoration: BoxDecoration(
                                        shape: BoxShape.circle,
                                        border: Border.all(color: Colors.white.withValues(alpha: 0.15), width: 3),
                                      ),
                                    ),
                                    Container(
                                      width: 60,
                                      height: 60,
                                      decoration: BoxDecoration(
                                        shape: BoxShape.circle,
                                        border: Border.all(color: Colors.white.withValues(alpha: 0.5), width: 2),
                                      ),
                                    ),
                                    UserAvatar(
                                      name: user?.name ?? 'U',
                                      imageUrl: user?.avatarUrl,
                                      radius: 27,
                                      backgroundColor: Colors.white.withValues(alpha: 0.2),
                                      textColor: Colors.white,
                                    ),
                                  ],
                                ),
                                const SizedBox(width: 16),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        'สวัสดี 👋',
                                        style: GoogleFonts.prompt(
                                          color: Colors.white.withValues(alpha: 0.75),
                                          fontSize: 13,
                                          fontWeight: FontWeight.w400,
                                        ),
                                      ),
                                      const SizedBox(height: 2),
                                      Text(
                                        user?.displayName ?? '',
                                        style: GoogleFonts.prompt(
                                          color: Colors.white,
                                          fontSize: 18,
                                          fontWeight: FontWeight.w700,
                                          height: 1.2,
                                        ),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                      const SizedBox(height: 4),
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                                        decoration: BoxDecoration(
                                          color: Colors.white.withValues(alpha: 0.18),
                                          borderRadius: BorderRadius.circular(20),
                                        ),
                                        child: Text(
                                          user?.roleLabel ?? '',
                                          style: GoogleFonts.prompt(
                                            color: Colors.white.withValues(alpha: 0.95),
                                            fontSize: 11,
                                            fontWeight: FontWeight.w500,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 20),
                            // Stats row
                            Container(
                              padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
                              decoration: BoxDecoration(
                                color: Colors.white.withValues(alpha: 0.1),
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(color: Colors.white.withValues(alpha: 0.15)),
                              ),
                              child: Row(
                                children: [
                                  _buildHeaderStat(
                                    icon: Icons.event_note_rounded,
                                    label: 'คำขอลา',
                                    value: '${_recentRequests.length}',
                                  ),
                                  _buildHeaderDivider(),
                                  _buildHeaderStat(
                                    icon: Icons.shield_rounded,
                                    label: 'เวรวันนี้',
                                    value: '${_todayGuards.length}',
                                  ),
                                  if (_pendingApprovals > 0) ...[
                                    _buildHeaderDivider(),
                                    _buildHeaderStat(
                                      icon: Icons.pending_actions_rounded,
                                      label: 'รออนุมัติ',
                                      value: '$_pendingApprovals',
                                      highlight: true,
                                    ),
                                  ],
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),

            // Content
            SliverToBoxAdapter(
              child: _isLoading
                  ? const Padding(
                      padding: EdgeInsets.all(60),
                      child: Center(child: CircularProgressIndicator(color: AppTheme.primary)),
                    )
                  : Container(
                      margin: const EdgeInsets.only(top: 8),
                      decoration: BoxDecoration(
                        color: AppTheme.background,
                        borderRadius: const BorderRadius.vertical(top: Radius.circular(32)),
                        boxShadow: [
                          BoxShadow(
                            color: AppTheme.primary.withValues(alpha: 0.08),
                            blurRadius: 24,
                            offset: const Offset(0, -8),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const SizedBox(height: 12),
                          Center(
                            child: Container(
                              width: 48,
                              height: 5,
                              decoration: BoxDecoration(
                                color: AppTheme.textMuted.withValues(alpha: 0.2),
                                borderRadius: BorderRadius.circular(20),
                              ),
                            ),
                          ),
                          const SizedBox(height: 8),

                          // Pending approvals banner
                          if (user != null && user.canApprove && _pendingApprovals > 0)
                            Padding(
                              padding: const EdgeInsets.fromLTRB(20, 16, 20, 0),
                              child: _buildPendingApprovalBanner(),
                            ),

                          // Quick Actions
                          Padding(
                            padding: const EdgeInsets.fromLTRB(20, 16, 20, 0),
                            child: _buildSectionHeader('เมนูดำเนินการ', Icons.grid_view_rounded),
                          ),
                          Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 20),
                            child: _buildQuickActions(),
                          ),

                          // Leave Balance
                          Padding(
                            padding: const EdgeInsets.fromLTRB(20, 32, 20, 14),
                            child: _buildAccentHeader(
                              title: 'วันลาคงเหลือ',
                              subtitle: 'ปีงบประมาณปัจจุบัน',
                              accentColors: [Color(0xFF3B82F6), Color(0xFF1E3A5F)],
                              actionLabel: 'ประวัติ',
                              onAction: () => Navigator.pushNamed(context, '/leave'),
                            ),
                          ),
                          _buildBalanceCards(),

                          // Today's Guard Duty
                          Padding(
                            padding: const EdgeInsets.fromLTRB(20, 32, 20, 14),
                            child: _buildAccentHeader(
                              title: 'ผู้เข้าเวรวันนี้',
                              subtitle: 'ตารางเวรประจำวัน',
                              accentColors: [Color(0xFF7C3AED), Color(0xFF4F46E5)],
                              actionLabel: 'ตารางเวร',
                              onAction: () => Navigator.pushNamed(context, '/duty-roster'),
                            ),
                          ),
                          Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 20),
                            child: _buildTodayGuards(),
                          ),

                          // News from nass.ac.th
                          Padding(
                            padding: const EdgeInsets.fromLTRB(20, 32, 20, 14),
                            child: _buildAccentHeader(
                              title: 'ข่าวสาร NASS',
                              subtitle: 'ข่าวสารจาก nass.ac.th',
                              accentColors: [Color(0xFF0EA5E9), Color(0xFF0369A1)],
                            ),
                          ),
                          _buildNewsSection(),

                          // Recent Requests
                          Padding(
                            padding: const EdgeInsets.fromLTRB(20, 32, 20, 14),
                            child: _buildAccentHeader(
                              title: 'คำขอล่าสุด',
                              subtitle: 'รายการที่ยื่นล่าสุด',
                              accentColors: [Color(0xFF10B981), Color(0xFF047857)],
                              actionLabel: 'ทั้งหมด',
                              onAction: () => Navigator.pushNamed(context, '/leave'),
                            ),
                          ),
                          Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 20),
                            child: _buildRecentRequests(),
                          ),

                          const SizedBox(height: 120),
                        ],
                      ),
                    ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildNotificationBell() {
    return Stack(
      children: [
        Container(
          margin: const EdgeInsets.all(4),
          decoration: BoxDecoration(
            color: Colors.white.withValues(alpha: 0.1),
            borderRadius: BorderRadius.circular(12),
          ),
          child: IconButton(
            onPressed: () {
              Navigator.push(context, MaterialPageRoute(builder: (_) => const NotificationScreen()));
            },
            icon: const Icon(Icons.notifications_outlined, color: Colors.white, size: 24),
          ),
        ),
        if (_unreadNotifications > 0)
          Positioned(
            right: 2,
            top: 2,
            child: Container(
              padding: const EdgeInsets.all(5),
              decoration: BoxDecoration(
                color: AppTheme.accent,
                shape: BoxShape.circle,
                border: Border.all(color: AppTheme.primaryDark, width: 2),
              ),
              child: Text(
                _unreadNotifications > 9 ? '9+' : '$_unreadNotifications',
                style: GoogleFonts.prompt(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w700),
              ),
            ),
          ),
      ],
    );
  }

  Widget _buildHeaderStat({
    required IconData icon,
    required String label,
    required String value,
    bool highlight = false,
  }) {
    final color = highlight ? const Color(0xFFFBBF24) : Colors.white;
    return Expanded(
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(icon, size: 13, color: color.withValues(alpha: 0.8)),
              const SizedBox(width: 4),
              Text(
                value,
                style: GoogleFonts.prompt(
                  color: color,
                  fontSize: 18,
                  fontWeight: FontWeight.w700,
                  height: 1,
                ),
              ),
            ],
          ),
          const SizedBox(height: 3),
          Text(
            label,
            style: GoogleFonts.prompt(
              color: Colors.white.withValues(alpha: 0.65),
              fontSize: 10,
              fontWeight: FontWeight.w400,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildHeaderDivider() {
    return Container(
      width: 1,
      height: 32,
      margin: const EdgeInsets.symmetric(horizontal: 8),
      color: Colors.white.withValues(alpha: 0.15),
    );
  }

  Widget _buildPendingApprovalBanner() {
    return Material(
      color: Colors.transparent,
      borderRadius: BorderRadius.circular(16),
      child: InkWell(
        onTap: () {},
        borderRadius: BorderRadius.circular(16),
        child: Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [AppTheme.accent.withValues(alpha: 0.08), AppTheme.warning.withValues(alpha: 0.04)],
        ),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.accent.withValues(alpha: 0.2)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: AppTheme.accent.withValues(alpha: 0.15),
              borderRadius: BorderRadius.circular(14),
            ),
            child: const Icon(Icons.pending_actions_rounded, color: AppTheme.accent, size: 24),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('รออนุมัติ', style: AppTheme.heading(15)),
                Text('มี $_pendingApprovals ใบลารอการอนุมัติ', style: AppTheme.body(13, color: AppTheme.textSecondary)),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: AppTheme.accent.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(10),
            ),
            child: const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: AppTheme.accent),
          ),
        ],
      ),
    )));
  }

  Widget _buildAccentHeader({
    required String title,
    required String subtitle,
    required List<Color> accentColors,
    String? actionLabel,
    VoidCallback? onAction,
  }) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Container(
          width: 4,
          height: 24,
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: accentColors,
            ),
            borderRadius: BorderRadius.circular(4),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: GoogleFonts.prompt(
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                  color: AppTheme.textPrimary,
                  height: 1.2,
                ),
              ),
              Text(
                subtitle,
                style: GoogleFonts.prompt(
                  fontSize: 11,
                  color: AppTheme.textMuted,
                  fontWeight: FontWeight.w400,
                ),
              ),
            ],
          ),
        ),
        if (actionLabel != null && onAction != null)
          GestureDetector(
            onTap: onAction,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: accentColors[0].withValues(alpha: 0.08),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: accentColors[0].withValues(alpha: 0.2)),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    actionLabel,
                    style: GoogleFonts.prompt(
                      fontSize: 12,
                      color: accentColors[0],
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(width: 2),
                  Icon(Icons.chevron_right_rounded, size: 14, color: accentColors[0]),
                ],
              ),
            ),
          ),
      ],
    );
  }

  Widget _buildSectionHeader(String title, IconData icon, {String? actionLabel, VoidCallback? onAction}) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: AppTheme.primary.withValues(alpha: 0.08),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, size: 18, color: AppTheme.primary),
        ),
        const SizedBox(width: 10),
        Expanded(child: Text(title, style: AppTheme.heading(16))),
        if (actionLabel != null && onAction != null)
          GestureDetector(
            onTap: onAction,
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(actionLabel, style: GoogleFonts.prompt(fontSize: 12, color: AppTheme.primary, fontWeight: FontWeight.w500)),
                const SizedBox(width: 2),
                const Icon(Icons.chevron_right_rounded, size: 16, color: AppTheme.primary),
              ],
            ),
          ),
      ],
    );
  }

  Widget _buildQuickActions() {
    final actions = [
      _ActionItem(icon: Icons.edit_note_rounded, label: 'ยื่นใบลา', color: AppTheme.primary,
          onTap: () => Navigator.pushNamed(context, '/leave/create')),
      _ActionItem(icon: Icons.swap_horiz_rounded, label: 'เปลี่ยนเวร', color: const Color(0xFF0EA5E9),
          onTap: () => Navigator.pushNamed(context, '/guard-change/create')),
      _ActionItem(icon: Icons.shield_rounded, label: 'ตารางเวร', color: const Color(0xFF7C3AED),
          onTap: () => Navigator.pushNamed(context, '/duty-roster')),
      _ActionItem(icon: Icons.notifications_active_rounded, label: 'แจ้งเตือน', color: AppTheme.accent,
          onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const NotificationScreen()))),
    ];
    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisSpacing: 12,
      mainAxisSpacing: 12,
      childAspectRatio: 2.4,
      children: actions.map((a) => _buildActionTile(a)).toList(),
    );
  }

  Widget _buildActionTile(_ActionItem a) {
    return Material(
      color: AppTheme.surface,
      borderRadius: BorderRadius.circular(16),
      child: InkWell(
        onTap: a.onTap,
        borderRadius: BorderRadius.circular(16),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(16),
            boxShadow: AppTheme.softShadow,
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: a.color.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(a.icon, color: a.color, size: 22),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Text(
                  a.label,
                  style: GoogleFonts.prompt(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: AppTheme.textPrimary,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildBalanceCards() {
    if (_balances.isEmpty) {
      return Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: AppTheme.surface,
            borderRadius: BorderRadius.circular(18),
            boxShadow: AppTheme.softShadow,
          ),
          child: Center(
            child: Column(
              children: [
                Icon(Icons.calendar_month_rounded, size: 36, color: AppTheme.textMuted.withValues(alpha: 0.4)),
                const SizedBox(height: 8),
                Text('ยังไม่มีข้อมูลวันลา', style: AppTheme.body(14, color: AppTheme.textMuted)),
              ],
            ),
          ),
        ),
      );
    }

    return SizedBox(
      height: 150,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        itemCount: _balances.length,
        separatorBuilder: (_, __) => const SizedBox(width: 12),
        itemBuilder: (_, i) {
          final b = _balances[i];
          final colors = _getBalanceColor(i);
          final percent = b.totalDays > 0 ? (b.remainingDays / b.totalDays) : 0.0;

          return Container(
            width: 165,
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: colors,
              ),
              borderRadius: BorderRadius.circular(20),
              boxShadow: [
                BoxShadow(
                  color: colors[0].withValues(alpha: 0.35),
                  blurRadius: 12,
                  offset: const Offset(0, 6),
                ),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  b.leaveType?.name ?? 'ลา',
                  style: GoogleFonts.prompt(
                    color: Colors.white.withValues(alpha: 0.9),
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text(
                      '${b.remainingDays.toStringAsFixed(0)}',
                      style: GoogleFonts.prompt(
                        color: Colors.white,
                        fontSize: 38,
                        fontWeight: FontWeight.w600,
                        height: 1,
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.only(bottom: 4, left: 4),
                      child: Text(
                        'วัน',
                        style: GoogleFonts.prompt(
                          color: Colors.white.withValues(alpha: 0.7),
                          fontSize: 13,
                        ),
                      ),
                    ),
                  ],
                ),
                // Progress bar
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(4),
                      child: LinearProgressIndicator(
                        value: percent.clamp(0.0, 1.0),
                        backgroundColor: Colors.white.withValues(alpha: 0.2),
                        valueColor: const AlwaysStoppedAnimation(Colors.white),
                        minHeight: 4,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'จาก ${b.totalDays.toStringAsFixed(0)} วัน',
                      style: GoogleFonts.prompt(
                        color: Colors.white.withValues(alpha: 0.6),
                        fontSize: 10,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  List<Color> _getBalanceColor(int index) {
    final palettes = [
      [const Color(0xFF1B2A4A), const Color(0xFF3B5998)],
      [const Color(0xFF047857), const Color(0xFF10B981)],
      [const Color(0xFFB45309), const Color(0xFFF59E0B)],
      [const Color(0xFF6D28D9), const Color(0xFF8B5CF6)],
      [const Color(0xFFBE123C), const Color(0xFFF43F5E)],
    ];
    return palettes[index % palettes.length];
  }

  Widget _buildTodayGuards() {
    if (_todayGuards.isEmpty) {
      return Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: AppTheme.surface,
          borderRadius: BorderRadius.circular(16),
          boxShadow: AppTheme.softShadow,
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: AppTheme.primary.withValues(alpha: 0.08),
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Icon(Icons.shield_outlined, color: AppTheme.primary, size: 22),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('ไม่มีข้อมูลเวรวันนี้', style: AppTheme.heading(14)),
                  Text('ยังไม่มีการกำหนดตารางเวรสำหรับวันนี้',
                      style: AppTheme.body(12, color: AppTheme.textMuted)),
                ],
              ),
            ),
          ],
        ),
      );
    }

    // Grouped single card with all positions
    final positionColors = {
      'senior_duty_officer': const Color(0xFF7C3AED),
      'duty_officer': AppTheme.primary,
      'assistant_duty_officer': const Color(0xFF0EA5E9),
    };
    final positionIcons = {
      'senior_duty_officer': Icons.military_tech_rounded,
      'duty_officer': Icons.shield_rounded,
      'assistant_duty_officer': Icons.person_rounded,
    };

    return Container(
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(16),
        boxShadow: AppTheme.softShadow,
      ),
      child: Column(
        children: [
          ..._todayGuards.asMap().entries.map((entry) {
            final i = entry.key;
            final g = entry.value;
            final posKey = g['position_key'] ?? 'duty_officer';
            final position = g['position_label'] ?? '-';
            final guardName = g['guard_name'] ?? g['name'] ?? '-';
            final guardRank = g['guard_rank'] ?? g['rank'] ?? '';
            final displayName = guardRank.isNotEmpty ? '$guardRank $guardName' : guardName;
            final color = positionColors[posKey] ?? AppTheme.primary;
            final icon = positionIcons[posKey] ?? Icons.shield_rounded;
            final isLast = i == _todayGuards.length - 1;

            final avatarUrl = g['avatar_url'] as String?;

            return Column(
              children: [
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                  child: Row(
                    children: [
                      // Colored left indicator
                      Container(
                        width: 4,
                        height: 44,
                        decoration: BoxDecoration(
                          color: color,
                          borderRadius: BorderRadius.circular(4),
                        ),
                      ),
                      const SizedBox(width: 12),
                      // Avatar with position icon badge
                      Stack(
                        clipBehavior: Clip.none,
                        children: [
                          UserAvatar(
                            name: guardName,
                            imageUrl: avatarUrl,
                            radius: 22,
                            backgroundColor: color.withValues(alpha: 0.12),
                            textColor: color,
                          ),
                          Positioned(
                            right: -4,
                            bottom: -4,
                            child: Container(
                              padding: const EdgeInsets.all(3),
                              decoration: BoxDecoration(
                                color: color,
                                shape: BoxShape.circle,
                                border: Border.all(color: Colors.white, width: 1.5),
                              ),
                              child: Icon(icon, color: Colors.white, size: 10),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              position,
                              style: GoogleFonts.prompt(
                                fontSize: 11,
                                color: color,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                            Text(
                              displayName,
                              style: GoogleFonts.prompt(
                                fontSize: 14,
                                fontWeight: FontWeight.w700,
                                color: AppTheme.textPrimary,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ],
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: AppTheme.success.withValues(alpha: 0.1),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: AppTheme.success.withValues(alpha: 0.3)),
                        ),
                        child: Text(
                          'เข้าเวร',
                          style: GoogleFonts.prompt(
                            fontSize: 10,
                            fontWeight: FontWeight.w600,
                            color: AppTheme.success,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                if (!isLast)
                  Divider(height: 1, indent: 50, endIndent: 16,
                      color: AppTheme.textMuted.withValues(alpha: 0.12)),
              ],
            );
          }),
          // View all link
          InkWell(
            onTap: () => Navigator.pushNamed(context, '/duty-roster'),
            borderRadius: const BorderRadius.vertical(bottom: Radius.circular(16)),
            child: Container(
              padding: const EdgeInsets.symmetric(vertical: 12),
              decoration: BoxDecoration(
                color: AppTheme.primary.withValues(alpha: 0.04),
                borderRadius: const BorderRadius.vertical(bottom: Radius.circular(16)),
                border: Border(top: BorderSide(color: AppTheme.primary.withValues(alpha: 0.08))),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    'ดูตารางเวรทั้งหมด',
                    style: GoogleFonts.prompt(
                      fontSize: 13,
                      color: AppTheme.primary,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(width: 4),
                  const Icon(Icons.arrow_forward_ios_rounded, size: 12, color: AppTheme.primary),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildNewsSection() {
    if (_news.isEmpty) {
      return Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: AppTheme.surface,
            borderRadius: BorderRadius.circular(18),
            boxShadow: AppTheme.softShadow,
          ),
          child: Center(
            child: Column(
              children: [
                Icon(Icons.newspaper_rounded, size: 36, color: AppTheme.textMuted.withValues(alpha: 0.4)),
                const SizedBox(height: 8),
                Text('กำลังโหลดข่าวสาร...', style: AppTheme.body(14, color: AppTheme.textMuted)),
              ],
            ),
          ),
        ),
      );
    }

    return SizedBox(
      height: 200,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        itemCount: _news.length,
        separatorBuilder: (_, __) => const SizedBox(width: 14),
        itemBuilder: (_, i) {
          final news = _news[i];
          return GestureDetector(
            onTap: () async {
              if (news.link != null) {
                final uri = Uri.tryParse(news.link!);
                if (uri != null) {
                  await launchUrl(uri, mode: LaunchMode.externalApplication);
                }
              }
            },
            child: Container(
              width: 260,
              decoration: BoxDecoration(
                color: AppTheme.surface,
                borderRadius: BorderRadius.circular(18),
                boxShadow: AppTheme.softShadow,
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Image
                  ClipRRect(
                    borderRadius: const BorderRadius.vertical(top: Radius.circular(18)),
                    child: Container(
                      height: 120,
                      width: double.infinity,
                      color: AppTheme.background,
                      child: news.imageUrl != null
                          ? CachedNetworkImage(
                              imageUrl: news.imageUrl!,
                              fit: BoxFit.cover,
                              httpHeaders: const {
                                'Referer': 'https://nass.ac.th',
                                'User-Agent': 'Mozilla/5.0 (Linux; Android 10) AppleWebKit/537.36',
                              },
                              placeholder: (_, __) => Container(
                                decoration: const BoxDecoration(gradient: AppTheme.primaryGradient),
                                child: const Center(
                                  child: Icon(Icons.newspaper_rounded, color: Colors.white54, size: 32),
                                ),
                              ),
                              errorWidget: (_, __, ___) => Container(
                                decoration: const BoxDecoration(gradient: AppTheme.primaryGradient),
                                child: const Center(
                                  child: Icon(Icons.newspaper_rounded, color: Colors.white54, size: 32),
                                ),
                              ),
                            )
                          : Container(
                              decoration: const BoxDecoration(gradient: AppTheme.primaryGradient),
                              child: const Center(
                                child: Icon(Icons.newspaper_rounded, color: Colors.white54, size: 32),
                              ),
                            ),
                    ),
                  ),
                  // Title
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.all(12),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Expanded(
                            child: Text(
                              news.title,
                              style: AppTheme.heading(12),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                          if (news.date != null && news.date!.isNotEmpty)
                            Text(news.date!, style: AppTheme.body(10, color: AppTheme.textMuted)),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildRecentRequests() {
    if (_recentRequests.isEmpty) {
      return Container(
        padding: const EdgeInsets.all(28),
        decoration: BoxDecoration(
          color: AppTheme.surface,
          borderRadius: BorderRadius.circular(18),
          boxShadow: AppTheme.softShadow,
        ),
        child: Center(
          child: Column(
            children: [
              Icon(Icons.inbox_rounded, size: 40, color: AppTheme.textMuted.withValues(alpha: 0.4)),
              const SizedBox(height: 8),
              Text('ยังไม่มีคำขอลา', style: AppTheme.body(14, color: AppTheme.textMuted)),
            ],
          ),
        ),
      );
    }

    return Column(
      children: _recentRequests.map((r) => _buildRequestItem(r)).toList(),
    );
  }

  Widget _buildRequestItem(LeaveRequest r) {
    final dateFormat = DateFormat('d MMM', 'th');
    String dateText = '';
    try {
      final start = DateTime.parse(r.startDate);
      final end = DateTime.parse(r.endDate);
      dateText = '${dateFormat.format(start)} - ${dateFormat.format(end)}';
    } catch (_) {
      dateText = '${r.startDate} - ${r.endDate}';
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      child: Material(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(18),
        child: InkWell(
          onTap: () => Navigator.pushNamed(context, '/leave/detail', arguments: r.id),
          borderRadius: BorderRadius.circular(18),
          child: Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(18),
              boxShadow: AppTheme.softShadow,
            ),
            child: Row(
              children: [
                Container(
                  width: 48,
                  height: 48,
                  decoration: BoxDecoration(
                    color: AppTheme.getStatusBgColor(r.status),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Icon(
                    _getLeaveTypeIcon(r.leaveType?.slug),
                    color: AppTheme.getStatusColor(r.status),
                    size: 22,
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(r.leaveType?.name ?? 'ลา', style: AppTheme.heading(14)),
                      const SizedBox(height: 2),
                      Text(
                        '$dateText (${r.totalDays.toStringAsFixed(0)} วัน)',
                        style: AppTheme.body(12, color: AppTheme.textSecondary),
                      ),
                    ],
                  ),
                ),
                StatusBadge(status: r.status, label: r.statusLabel),
              ],
            ),
          ),
        ),
      ),
    );
  }

  IconData _getLeaveTypeIcon(String? slug) {
    switch (slug) {
      case 'vacation':
        return Icons.beach_access_rounded;
      case 'sick':
        return Icons.local_hospital_rounded;
      case 'personal':
        return Icons.person_rounded;
      case 'temporary':
        return Icons.timer_rounded;
      default:
        return Icons.event_note_rounded;
    }
  }
}

class _ActionItem {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;

  const _ActionItem({
    required this.icon,
    required this.label,
    required this.color,
    required this.onTap,
  });
}
