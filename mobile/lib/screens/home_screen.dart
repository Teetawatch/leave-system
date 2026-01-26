import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import 'dart:math' as math;
import '../config/app_theme.dart';
import '../providers/auth_provider.dart';
import '../providers/leave_provider.dart';
import 'leave_request_screen.dart';
import 'leave_history_screen.dart';
import 'guard_change_request_screen.dart';
import 'guard_change_history_screen.dart';
import 'guard_change_approvals_screen.dart';
import '../providers/news_provider.dart';
import '../models/news_model.dart';
import 'package:url_launcher/url_launcher.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> with TickerProviderStateMixin {
  late AnimationController _animationController;
  late AnimationController _pulseController;
  late Animation<double> _pulseAnimation;
  late ScrollController _scrollController;
  double _scrollOffset = 0;

  @override
  void initState() {
    super.initState();
    _scrollController = ScrollController()
      ..addListener(() {
        setState(() {
          _scrollOffset = _scrollController.offset;
        });
      });

    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1200),
    )..forward();

    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 2000),
    )..repeat(reverse: true);

    _pulseAnimation = Tween<double>(begin: 1.0, end: 1.05).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );

    WidgetsBinding.instance.addPostFrameCallback((_) {
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
    });
  }

  @override
  void dispose() {
    _animationController.dispose();
    _pulseController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  Future<void> _refreshData() async {
    HapticFeedback.mediumImpact();
    final leaveProvider = Provider.of<LeaveProvider>(context, listen: false);
    final newsProvider = Provider.of<NewsProvider>(context, listen: false);
    await Future.wait([
      leaveProvider.fetchLeaveBalances(),
      leaveProvider.fetchMyRequests(),
      newsProvider.fetchLatestNews(),
    ]);
  }

  String _getGreeting() {
    final hour = DateTime.now().hour;
    if (hour < 12) return 'สวัสดีตอนเช้า';
    if (hour < 17) return 'สวัสดีตอนบ่าย';
    return 'สวัสดีตอนเย็น';
  }

  String _getGreetingEmoji() {
    final hour = DateTime.now().hour;
    if (hour < 12) return '☀️';
    if (hour < 17) return '🌤️';
    return '🌙';
  }

  @override
  Widget build(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).user;
    final leaveProvider = Provider.of<LeaveProvider>(context);
    final newsProvider = Provider.of<NewsProvider>(context);
    final size = MediaQuery.of(context).size;

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark.copyWith(
        statusBarColor: Colors.transparent,
      ),
      child: Scaffold(
        backgroundColor: const Color(0xFFF8FAFF),
        body: Stack(
          children: [
            // Animated Background
            _buildAnimatedBackground(size),

            // Main Content
            RefreshIndicator(
              onRefresh: _refreshData,
              color: AppTheme.primary,
              backgroundColor: Colors.white,
              edgeOffset: 120,
              displacement: 60,
              child: CustomScrollView(
                controller: _scrollController,
                physics: const BouncingScrollPhysics(
                  parent: AlwaysScrollableScrollPhysics(),
                ),
                slivers: [
                  // Premium Header with Glassmorphism
                  SliverToBoxAdapter(child: _buildPremiumHeader(user, size)),

                  // Quick Stats Cards
                  SliverToBoxAdapter(
                    child: _buildQuickStatsSection(leaveProvider),
                  ),
                  // Quick Actions Grid
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.fromLTRB(24, 32, 24, 16),
                      child: _buildSectionHeader(
                        '⚡',
                        'เมนูด่วน',
                        'เข้าถึงฟีเจอร์ที่ใช้บ่อย',
                      ),
                    ),
                  ),

                  SliverPadding(
                    padding: const EdgeInsets.symmetric(horizontal: 24),
                    sliver: SliverToBoxAdapter(child: _buildQuickActionsGrid()),
                  ),

                  // News Section
                  if (newsProvider.newsList.isNotEmpty ||
                      newsProvider.isLoading)
                    SliverToBoxAdapter(
                      child: Padding(
                        padding: const EdgeInsets.fromLTRB(24, 32, 24, 16),
                        child: _buildSectionHeader(
                          '📰',
                          'ข่าวสารประชาสัมพันธ์',
                          'อัพเดทล่าสุดจากหน่วยงาน',
                        ),
                      ),
                    ),

                  SliverToBoxAdapter(child: _buildNewsCarousel(newsProvider)),

                  // Recent Requests Section
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.fromLTRB(24, 32, 24, 8),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          _buildSectionHeader('📋', 'คำขอล่าสุด', null),
                          _buildViewAllButton(),
                        ],
                      ),
                    ),
                  ),

                  // Recent Request List
                  _buildRecentRequestsSliver(leaveProvider),

                  // Bottom Padding
                  const SliverToBoxAdapter(child: SizedBox(height: 100)),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAnimatedBackground(Size size) {
    return Stack(
      children: [
        // Base gradient
        Container(
          height: size.height,
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Color(0xFFF8FAFF), Color(0xFFFFFFFF), Color(0xFFF0F4FF)],
              stops: [0.0, 0.5, 1.0],
            ),
          ),
        ),

        // Animated blob 1
        AnimatedBuilder(
          animation: _pulseAnimation,
          builder: (context, child) {
            return Positioned(
              top: -80 + (_scrollOffset * 0.3),
              right: -60,
              child: Transform.scale(
                scale: _pulseAnimation.value,
                child: Container(
                  width: 280,
                  height: 280,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    gradient: RadialGradient(
                      colors: [
                        AppTheme.primary.withOpacity(0.15),
                        AppTheme.primary.withOpacity(0.05),
                        Colors.transparent,
                      ],
                    ),
                  ),
                ),
              ),
            );
          },
        ),

        // Animated blob 2
        AnimatedBuilder(
          animation: _pulseAnimation,
          builder: (context, child) {
            return Positioned(
              top: 400 - (_scrollOffset * 0.2),
              left: -100,
              child: Transform.scale(
                scale: 2 - _pulseAnimation.value,
                child: Container(
                  width: 250,
                  height: 250,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    gradient: RadialGradient(
                      colors: [
                        const Color(0xFF6366F1).withOpacity(0.1),
                        const Color(0xFF6366F1).withOpacity(0.03),
                        Colors.transparent,
                      ],
                    ),
                  ),
                ),
              ),
            );
          },
        ),

        // Mesh gradient overlay
        Positioned(
          bottom: 0,
          left: 0,
          right: 0,
          child: Container(
            height: 300,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
                colors: [
                  Colors.transparent,
                  const Color(0xFFEEF2FF).withOpacity(0.5),
                ],
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildPremiumHeader(dynamic user, Size size) {
    final double headerOpacity = (1 - (_scrollOffset / 150)).clamp(0.0, 1.0);

    return Container(
      padding: EdgeInsets.fromLTRB(
        24,
        MediaQuery.of(context).padding.top + 20,
        24,
        24,
      ),
      child: Column(
        children: [
          Row(
            children: [
              // Avatar with gradient border
              Container(
                padding: const EdgeInsets.all(3),
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: const LinearGradient(
                    colors: [
                      Color(0xFF6366F1),
                      Color(0xFF8B5CF6),
                      Color(0xFFEC4899),
                    ],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF6366F1).withOpacity(0.3),
                      blurRadius: 20,
                      offset: const Offset(0, 8),
                    ),
                  ],
                ),
                child: Container(
                  padding: const EdgeInsets.all(2),
                  decoration: const BoxDecoration(
                    shape: BoxShape.circle,
                    color: Colors.white,
                  ),
                  child: CircleAvatar(
                    radius: 28,
                    backgroundColor: const Color(0xFF6366F1).withOpacity(0.1),
                    child: Text(
                      (user?.name ?? 'U')[0].toUpperCase(),
                      style: const TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.w900,
                        color: Color(0xFF6366F1),
                      ),
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Opacity(
                  opacity: headerOpacity,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Text(
                            _getGreeting(),
                            style: TextStyle(
                              color: AppTheme.textSub.withOpacity(0.8),
                              fontSize: 14,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          const SizedBox(width: 6),
                          Text(
                            _getGreetingEmoji(),
                            style: const TextStyle(fontSize: 16),
                          ),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Text(
                        user?.fullName ?? 'พนักงาน',
                        style: const TextStyle(
                          color: Color(0xFF1E293B),
                          fontSize: 24,
                          fontWeight: FontWeight.w900,
                          letterSpacing: -0.5,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
              ),
              _buildGlassNotificationButton(),
            ],
          ),

          // Role Badge
          if (user != null && user.role != 'employee')
            Opacity(
              opacity: headerOpacity,
              child: Container(
                margin: const EdgeInsets.only(top: 20),
                padding: const EdgeInsets.symmetric(
                  horizontal: 16,
                  vertical: 10,
                ),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [
                      Colors.white.withOpacity(0.9),
                      Colors.white.withOpacity(0.7),
                    ],
                  ),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Colors.white.withOpacity(0.5)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.05),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(6),
                      decoration: BoxDecoration(
                        color: const Color(0xFF6366F1).withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(
                        Icons.verified_rounded,
                        color: Color(0xFF6366F1),
                        size: 18,
                      ),
                    ),
                    const SizedBox(width: 10),
                    Text(
                      _getRoleDisplayName(user.role),
                      style: const TextStyle(
                        color: Color(0xFF1E293B),
                        fontWeight: FontWeight.w700,
                        fontSize: 13,
                      ),
                    ),
                  ],
                ),
              ),
            ),
        ],
      ),
    );
  }

  String _getRoleDisplayName(String role) {
    switch (role) {
      case 'admin':
        return '👑 ผู้ดูแลระบบ';
      case 'manager':
        return '💼 ผู้จัดการ';
      case 'supervisor':
        return '👔 หัวหน้างาน';
      default:
        return '👤 พนักงาน';
    }
  }

  Widget _buildGlassNotificationButton() {
    return ClipRRect(
      borderRadius: BorderRadius.circular(16),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
        child: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: [
                Colors.white.withOpacity(0.8),
                Colors.white.withOpacity(0.6),
              ],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: Colors.white.withOpacity(0.5)),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.05),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Stack(
            children: [
              IconButton(
                onPressed: () {
                  HapticFeedback.lightImpact();
                },
                icon: const Icon(
                  Icons.notifications_none_rounded,
                  color: Color(0xFF1E293B),
                  size: 24,
                ),
              ),
              Positioned(
                right: 10,
                top: 10,
                child: Container(
                  width: 10,
                  height: 10,
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFFEF4444), Color(0xFFF97316)],
                    ),
                    shape: BoxShape.circle,
                    border: Border.all(color: Colors.white, width: 2),
                    boxShadow: [
                      BoxShadow(
                        color: const Color(0xFFEF4444).withOpacity(0.5),
                        blurRadius: 6,
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

  Widget _buildQuickStatsSection(LeaveProvider provider) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 24),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF6366F1), Color(0xFF8B5CF6)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF6366F1).withOpacity(0.4),
            blurRadius: 30,
            offset: const Offset(0, 15),
          ),
        ],
      ),
      child: Stack(
        children: [
          // Decorative elements
          Positioned(
            top: -30,
            right: -20,
            child: Container(
              width: 100,
              height: 100,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.white.withOpacity(0.1),
              ),
            ),
          ),
          Positioned(
            bottom: -40,
            left: -30,
            child: Container(
              width: 120,
              height: 120,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.white.withOpacity(0.05),
              ),
            ),
          ),

          // Content
          Column(
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  _buildMiniStat(
                    icon: Icons.calendar_today_rounded,
                    label: 'วันลาคงเหลือ',
                    value: _getTotalRemainingDays(provider),
                  ),
                  Container(
                    width: 1,
                    height: 50,
                    color: Colors.white.withOpacity(0.2),
                  ),
                  _buildMiniStat(
                    icon: Icons.pending_actions_rounded,
                    label: 'รออนุมัติ',
                    value: provider.myRequests
                        .where((r) => r.status == 'pending')
                        .length
                        .toString(),
                  ),
                  Container(
                    width: 1,
                    height: 50,
                    color: Colors.white.withOpacity(0.2),
                  ),
                  _buildMiniStat(
                    icon: Icons.check_circle_outline_rounded,
                    label: 'อนุมัติแล้ว',
                    value: provider.myRequests
                        .where((r) => r.status == 'approved')
                        .length
                        .toString(),
                  ),
                ],
              ),
            ],
          ),
        ],
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

  Widget _buildMiniStat({
    required IconData icon,
    required String label,
    required String value,
  }) {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.15),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Icon(icon, color: Colors.white, size: 20),
        ),
        const SizedBox(height: 10),
        Text(
          value,
          style: const TextStyle(
            color: Colors.white,
            fontSize: 24,
            fontWeight: FontWeight.w900,
          ),
        ),
        const SizedBox(height: 2),
        Text(
          label,
          style: TextStyle(
            color: Colors.white.withOpacity(0.8),
            fontSize: 11,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }

  Widget _buildSectionHeader(String emoji, String title, String? subtitle) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(emoji, style: const TextStyle(fontSize: 20)),
            const SizedBox(width: 8),
            Text(
              title,
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w900,
                color: Color(0xFF1E293B),
                letterSpacing: -0.5,
              ),
            ),
          ],
        ),
        if (subtitle != null) ...[
          const SizedBox(height: 4),
          Text(
            subtitle,
            style: TextStyle(
              fontSize: 13,
              color: AppTheme.textSub.withOpacity(0.7),
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ],
    );
  }

  Widget _buildViewAllButton() {
    return GestureDetector(
      onTap: () {
        HapticFeedback.lightImpact();
        Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => const ActivityScreen()),
        );
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        decoration: BoxDecoration(
          color: const Color(0xFF6366F1).withOpacity(0.1),
          borderRadius: BorderRadius.circular(12),
        ),
        child: const Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              'ดูทั้งหมด',
              style: TextStyle(
                fontWeight: FontWeight.w700,
                color: Color(0xFF6366F1),
                fontSize: 13,
              ),
            ),
            SizedBox(width: 4),
            Icon(
              Icons.arrow_forward_rounded,
              size: 16,
              color: Color(0xFF6366F1),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBalanceCards(LeaveProvider provider) {
    if (provider.isLoading && provider.balances.isEmpty) {
      return _buildBalanceShimmer();
    }

    if (provider.balances.isEmpty) {
      return _buildEmptyBalanceCard();
    }

    return Column(
      children: provider.balances.map((bal) {
        final slug = bal.leaveType?.slug ?? '';
        final gradientColors = _getLeaveTypeGradient(slug);
        final title = bal.leaveType?.name ?? 'ไม่ระบุ';
        final remaining = bal.remainingDays.toString().replaceAll(
          RegExp(r'\.0$'),
          '',
        );
        final total = bal.totalDays.toString().replaceAll(RegExp(r'\.0$'), '');
        final progress = (bal.remainingDays / bal.totalDays).clamp(0.0, 1.0);

        return TweenAnimationBuilder<double>(
          tween: Tween(begin: 0.0, end: 1.0),
          duration: const Duration(milliseconds: 600),
          curve: Curves.easeOutCubic,
          builder: (context, value, child) {
            return Transform.translate(
              offset: Offset(0, 20 * (1 - value)),
              child: Opacity(opacity: value, child: child),
            );
          },
          child: Container(
            margin: const EdgeInsets.only(bottom: 16),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(28),
              boxShadow: [
                BoxShadow(
                  color: gradientColors[0].withOpacity(0.3),
                  blurRadius: 30,
                  offset: const Offset(0, 15),
                ),
              ],
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(28),
              child: Stack(
                children: [
                  // Gradient Background
                  Container(
                    height: 140,
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: gradientColors,
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                    ),
                  ),

                  // Mesh pattern overlay
                  Positioned.fill(
                    child: CustomPaint(painter: _MeshPatternPainter()),
                  ),

                  // Content
                  Container(
                    height: 140,
                    padding: const EdgeInsets.all(24),
                    child: Row(
                      children: [
                        // Icon with glass effect
                        ClipRRect(
                          borderRadius: BorderRadius.circular(20),
                          child: BackdropFilter(
                            filter: ImageFilter.blur(sigmaX: 5, sigmaY: 5),
                            child: Container(
                              width: 60,
                              height: 60,
                              decoration: BoxDecoration(
                                color: Colors.white.withOpacity(0.2),
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(
                                  color: Colors.white.withOpacity(0.3),
                                ),
                              ),
                              child: Icon(
                                _getLeaveTypeIcon(slug),
                                color: Colors.white,
                                size: 28,
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(width: 20),

                        // Info
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Text(
                                title,
                                style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 18,
                                  fontWeight: FontWeight.w800,
                                  letterSpacing: -0.3,
                                ),
                              ),
                              const SizedBox(height: 6),
                              Text(
                                'สิทธิ์ทั้งหมด $total วัน',
                                style: TextStyle(
                                  color: Colors.white.withOpacity(0.8),
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                              const SizedBox(height: 12),
                              // Progress bar
                              Stack(
                                children: [
                                  Container(
                                    height: 6,
                                    decoration: BoxDecoration(
                                      color: Colors.white.withOpacity(0.25),
                                      borderRadius: BorderRadius.circular(10),
                                    ),
                                  ),
                                  FractionallySizedBox(
                                    widthFactor: progress,
                                    child: Container(
                                      height: 6,
                                      decoration: BoxDecoration(
                                        color: Colors.white,
                                        borderRadius: BorderRadius.circular(10),
                                        boxShadow: [
                                          BoxShadow(
                                            color: Colors.white.withOpacity(
                                              0.5,
                                            ),
                                            blurRadius: 8,
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(width: 16),

                        // Days counter
                        Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            ShaderMask(
                              shaderCallback: (bounds) => const LinearGradient(
                                colors: [Colors.white, Colors.white70],
                                begin: Alignment.topCenter,
                                end: Alignment.bottomCenter,
                              ).createShader(bounds),
                              child: Text(
                                remaining,
                                style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 42,
                                  fontWeight: FontWeight.w900,
                                  height: 1,
                                ),
                              ),
                            ),
                            const SizedBox(height: 4),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 10,
                                vertical: 4,
                              ),
                              decoration: BoxDecoration(
                                color: Colors.white.withOpacity(0.2),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: const Text(
                                'วันคงเหลือ',
                                style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 10,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      }).toList(),
    );
  }

  Widget _buildBalanceShimmer() {
    return Column(
      children: List.generate(
        2,
        (index) => Container(
          margin: const EdgeInsets.only(bottom: 16),
          height: 140,
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(28),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.05),
                blurRadius: 20,
                offset: const Offset(0, 10),
              ),
            ],
          ),
          child: const Center(child: CircularProgressIndicator()),
        ),
      ),
    );
  }

  Widget _buildEmptyBalanceCard() {
    return Container(
      padding: const EdgeInsets.all(32),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: const Color(0xFF6366F1).withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.inbox_rounded,
              color: Color(0xFF6366F1),
              size: 32,
            ),
          ),
          const SizedBox(height: 16),
          const Text(
            'ไม่พบข้อมูลสิทธิ์คงเหลือ',
            style: TextStyle(
              color: Color(0xFF64748B),
              fontWeight: FontWeight.w600,
              fontSize: 14,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildQuickActionsGrid() {
    final actions = [
      _QuickAction(
        title: 'ลางาน',
        subtitle: 'เขียนใบลา',
        icon: Icons.edit_calendar_rounded,
        gradient: [const Color(0xFF6366F1), const Color(0xFF8B5CF6)],
        onTap: () => Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => const LeaveRequestScreen()),
        ),
      ),
      _QuickAction(
        title: 'ประวัติ',
        subtitle: 'ดูการลาย้อนหลัง',
        icon: Icons.history_rounded,
        gradient: [const Color(0xFF10B981), const Color(0xFF34D399)],
        onTap: () => Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => const ActivityScreen()),
        ),
      ),
      _QuickAction(
        title: 'เปลี่ยนยาม',
        subtitle: 'ส่งคำขอใหม่',
        icon: Icons.swap_horiz_rounded,
        gradient: [const Color(0xFFF59E0B), const Color(0xFFFBBF24)],
        onTap: () => Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => const GuardChangeRequestScreen()),
        ),
      ),
      _QuickAction(
        title: 'รายการ',
        subtitle: 'ประวัติการเปลี่ยน',
        icon: Icons.assignment_rounded,
        gradient: [const Color(0xFF06B6D4), const Color(0xFF22D3EE)],
        onTap: () => Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => const GuardChangeHistoryScreen()),
        ),
      ),
    ];

    return Column(
      children: [
        Row(
          children: [
            Expanded(child: _buildQuickActionCard(actions[0])),
            const SizedBox(width: 12),
            Expanded(child: _buildQuickActionCard(actions[1])),
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(child: _buildQuickActionCard(actions[2])),
            const SizedBox(width: 12),
            Expanded(child: _buildQuickActionCard(actions[3])),
          ],
        ),
        const SizedBox(height: 12),
        _buildLargeActionCard(),
      ],
    );
  }

  Widget _buildQuickActionCard(_QuickAction action) {
    return GestureDetector(
      onTap: () {
        HapticFeedback.lightImpact();
        action.onTap();
      },
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(
              color: action.gradient[0].withOpacity(0.1),
              blurRadius: 20,
              offset: const Offset(0, 8),
            ),
          ],
          border: Border.all(color: action.gradient[0].withOpacity(0.1)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                gradient: LinearGradient(colors: action.gradient),
                borderRadius: BorderRadius.circular(14),
                boxShadow: [
                  BoxShadow(
                    color: action.gradient[0].withOpacity(0.3),
                    blurRadius: 12,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Icon(action.icon, color: Colors.white, size: 22),
            ),
            const SizedBox(height: 16),
            Text(
              action.title,
              style: const TextStyle(
                fontWeight: FontWeight.w800,
                fontSize: 16,
                color: Color(0xFF1E293B),
              ),
            ),
            const SizedBox(height: 2),
            Text(
              action.subtitle,
              style: const TextStyle(
                color: Color(0xFF94A3B8),
                fontSize: 12,
                fontWeight: FontWeight.w500,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLargeActionCard() {
    return GestureDetector(
      onTap: () {
        HapticFeedback.lightImpact();
        Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => const GuardChangeApprovalsScreen()),
        );
      },
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: [const Color(0xFF1E293B), const Color(0xFF334155)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF1E293B).withOpacity(0.3),
              blurRadius: 20,
              offset: const Offset(0, 10),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.1),
                borderRadius: BorderRadius.circular(16),
              ),
              child: const Icon(
                Icons.notification_important_rounded,
                color: Colors.white,
                size: 24,
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'คำขอเปลี่ยนยามถึงฉัน',
                    style: TextStyle(
                      fontWeight: FontWeight.w800,
                      fontSize: 15,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'ตรวจสอบและดำเนินการยืนยัน',
                    style: TextStyle(
                      color: Colors.white.withOpacity(0.7),
                      fontSize: 12,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],
              ),
            ),
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Icon(
                Icons.arrow_forward_rounded,
                color: Colors.white,
                size: 20,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildNewsCarousel(NewsProvider provider) {
    if (provider.isLoading && provider.newsList.isEmpty) {
      return const SizedBox(
        height: 200,
        child: Center(child: CircularProgressIndicator()),
      );
    }

    if (provider.newsList.isEmpty) {
      return const SizedBox.shrink();
    }

    return SizedBox(
      height: 220,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.symmetric(horizontal: 20),
        itemCount: provider.newsList.length,
        itemBuilder: (context, index) {
          final news = provider.newsList[index];
          return _buildNewsCard(news, index);
        },
      ),
    );
  }

  Widget _buildNewsCard(dynamic news, int index) {
    return GestureDetector(
      onTap: () async {
        HapticFeedback.lightImpact();
        final url = Uri.parse(news.link);
        if (await canLaunchUrl(url)) {
          await launchUrl(url, mode: LaunchMode.externalApplication);
        }
      },
      child: TweenAnimationBuilder<double>(
        tween: Tween(begin: 0.0, end: 1.0),
        duration: Duration(milliseconds: 400 + (index * 100)),
        curve: Curves.easeOutCubic,
        builder: (context, value, child) {
          return Transform.translate(
            offset: Offset(30 * (1 - value), 0),
            child: Opacity(opacity: value, child: child),
          );
        },
        child: Container(
          width: 300,
          margin: const EdgeInsets.only(right: 16),
          child: Stack(
            children: [
              // Card background
              Container(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(28),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.1),
                      blurRadius: 25,
                      offset: const Offset(0, 12),
                    ),
                  ],
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(28),
                  child: Stack(
                    fit: StackFit.expand,
                    children: [
                      // Image
                      Image.network(
                        news.imageUrl,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) {
                          return Container(
                            color: const Color(0xFF6366F1),
                            child: const Icon(
                              Icons.newspaper_rounded,
                              color: Colors.white54,
                              size: 48,
                            ),
                          );
                        },
                      ),

                      // Gradient overlay
                      Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                            colors: [
                              Colors.transparent,
                              Colors.black.withOpacity(0.3),
                              Colors.black.withOpacity(0.85),
                            ],
                            stops: const [0.0, 0.5, 1.0],
                          ),
                        ),
                      ),

                      // Content
                      Positioned(
                        left: 20,
                        right: 20,
                        bottom: 20,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Category tag
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 10,
                                vertical: 5,
                              ),
                              decoration: BoxDecoration(
                                color: Colors.white.withOpacity(0.2),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: const Text(
                                '📢 ข่าวสาร',
                                style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 10,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                            ),
                            const SizedBox(height: 10),
                            // Title
                            Text(
                              news.title,
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 16,
                                fontWeight: FontWeight.w800,
                                height: 1.3,
                              ),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                            const SizedBox(height: 8),
                            // Date
                            Row(
                              children: [
                                Icon(
                                  Icons.access_time_rounded,
                                  size: 12,
                                  color: Colors.white.withOpacity(0.7),
                                ),
                                const SizedBox(width: 4),
                                Text(
                                  news.date.split('T')[0],
                                  style: TextStyle(
                                    color: Colors.white.withOpacity(0.7),
                                    fontSize: 11,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ],
                            ),
                          ],
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

  Widget _buildRecentRequestsSliver(LeaveProvider provider) {
    if (provider.isLoading && provider.myRequests.isEmpty) {
      return const SliverToBoxAdapter(
        child: Center(
          child: Padding(
            padding: EdgeInsets.all(40),
            child: CircularProgressIndicator(),
          ),
        ),
      );
    }

    if (provider.myRequests.isEmpty) {
      return SliverToBoxAdapter(
        child: Container(
          margin: const EdgeInsets.symmetric(horizontal: 24),
          padding: const EdgeInsets.all(40),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: const Color(0xFFE2E8F0)),
          ),
          child: Column(
            children: [
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: const Color(0xFF6366F1).withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.event_busy_rounded,
                  color: Color(0xFF6366F1),
                  size: 32,
                ),
              ),
              const SizedBox(height: 16),
              const Text(
                'ไม่มีประวัติการลาล่าสุด',
                style: TextStyle(
                  color: Color(0xFF64748B),
                  fontWeight: FontWeight.w600,
                  fontSize: 14,
                ),
              ),
            ],
          ),
        ),
      );
    }

    final displayRequests = provider.myRequests.take(3).toList();

    return SliverList(
      delegate: SliverChildBuilderDelegate((context, index) {
        return _buildRecentRequestCard(displayRequests[index], index);
      }, childCount: displayRequests.length),
    );
  }

  Widget _buildRecentRequestCard(dynamic request, int index) {
    final statusColor = _getStatusColor(request.status);
    final statusGradient = _getStatusGradient(request.status);

    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0.0, end: 1.0),
      duration: Duration(milliseconds: 400 + (index * 100)),
      curve: Curves.easeOutCubic,
      builder: (context, value, child) {
        return Transform.translate(
          offset: Offset(0, 20 * (1 - value)),
          child: Opacity(opacity: value, child: child),
        );
      },
      child: Container(
        margin: const EdgeInsets.fromLTRB(24, 0, 24, 12),
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(22),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 15,
              offset: const Offset(0, 5),
            ),
          ],
          border: Border.all(color: const Color(0xFFF1F5F9)),
        ),
        child: Row(
          children: [
            // Icon with gradient
            Container(
              width: 52,
              height: 52,
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [
                    statusColor.withOpacity(0.15),
                    statusColor.withOpacity(0.05),
                  ],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Icon(
                _getLeaveTypeIcon(request.leaveType?.slug ?? ''),
                color: statusColor,
                size: 24,
              ),
            ),
            const SizedBox(width: 16),
            // Info
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    request.leaveType?.name ?? 'ไม่ระบุ',
                    style: const TextStyle(
                      fontWeight: FontWeight.w800,
                      fontSize: 15,
                      color: Color(0xFF1E293B),
                    ),
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      Icon(
                        Icons.date_range_rounded,
                        size: 14,
                        color: const Color(0xFF94A3B8),
                      ),
                      const SizedBox(width: 4),
                      Text(
                        '${request.formattedStartDate} - ${request.formattedEndDate}',
                        style: const TextStyle(
                          color: Color(0xFF94A3B8),
                          fontSize: 12,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            // Status badge
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                gradient: LinearGradient(colors: statusGradient),
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color: statusGradient[0].withOpacity(0.3),
                    blurRadius: 8,
                    offset: const Offset(0, 3),
                  ),
                ],
              ),
              child: Text(
                _getStatusText(request.status),
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 11,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  List<Color> _getLeaveTypeGradient(String slug) {
    switch (slug) {
      case 'vacation':
        return [const Color(0xFF6366F1), const Color(0xFF8B5CF6)];
      case 'sick':
        return [const Color(0xFFEF4444), const Color(0xFFF97316)];
      case 'personal':
        return [const Color(0xFF10B981), const Color(0xFF34D399)];
      default:
        return [const Color(0xFFF59E0B), const Color(0xFFFBBF24)];
    }
  }

  IconData _getLeaveTypeIcon(String slug) {
    switch (slug) {
      case 'vacation':
        return Icons.beach_access_rounded;
      case 'sick':
        return Icons.medication_rounded;
      case 'personal':
        return Icons.person_rounded;
      default:
        return Icons.event_note_rounded;
    }
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'approved':
        return const Color(0xFF10B981);
      case 'rejected':
        return const Color(0xFFEF4444);
      default:
        return const Color(0xFFF59E0B);
    }
  }

  List<Color> _getStatusGradient(String status) {
    switch (status) {
      case 'approved':
        return [const Color(0xFF10B981), const Color(0xFF34D399)];
      case 'rejected':
        return [const Color(0xFFEF4444), const Color(0xFFF97316)];
      default:
        return [const Color(0xFFF59E0B), const Color(0xFFFBBF24)];
    }
  }

  String _getStatusText(String status) {
    switch (status) {
      case 'approved':
        return 'อนุมัติ';
      case 'rejected':
        return 'ปฏิเสธ';
      default:
        return 'รออนุมัติ';
    }
  }
}

// Custom painter for mesh pattern
class _MeshPatternPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.white.withOpacity(0.05)
      ..style = PaintingStyle.fill;

    // Draw decorative circles
    canvas.drawCircle(Offset(size.width * 0.9, size.height * 0.2), 60, paint);

    canvas.drawCircle(Offset(size.width * 0.1, size.height * 0.8), 40, paint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}

// Quick action data class
class _QuickAction {
  final String title;
  final String subtitle;
  final IconData icon;
  final List<Color> gradient;
  final VoidCallback onTap;

  _QuickAction({
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.gradient,
    required this.onTap,
  });
}
