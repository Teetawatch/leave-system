import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../providers/auth_provider.dart';
import '../providers/leave_provider.dart';
import 'leave_request_screen.dart';
import 'leave_history_screen.dart';
import 'guard_change_request_screen.dart';
import 'guard_change_history_screen.dart';
import 'guard_change_approvals_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _animationController;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1000),
    )..forward();

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
    });
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  Future<void> _refreshData() async {
    final leaveProvider = Provider.of<LeaveProvider>(context, listen: false);
    await Future.wait([
      leaveProvider.fetchLeaveBalances(),
      leaveProvider.fetchMyRequests(),
    ]);
  }

  String _getGreeting() {
    final hour = DateTime.now().hour;
    if (hour < 12) return 'สวัสดีตอนเช้า';
    if (hour < 17) return 'สวัสดีตอนบ่าย';
    return 'สวัสดีตอนเย็น';
  }

  @override
  Widget build(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).user;
    final leaveProvider = Provider.of<LeaveProvider>(context);
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          // 1. Background Design (consistent with Login)
          Container(
            height: size.height,
            width: size.width,
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [Color(0xFFF8FAFC), Colors.white, Color(0xFFEEF2FF)],
              ),
            ),
          ),
          _buildFloatingCircle(
            top: -100,
            right: -80,
            size: 300,
            color: AppTheme.primary.withOpacity(0.08),
          ),
          _buildFloatingCircle(
            bottom: -50,
            left: -50,
            size: 250,
            color: AppTheme.secondary.withOpacity(0.05),
          ),

          RefreshIndicator(
            onRefresh: _refreshData,
            color: AppTheme.primary,
            edgeOffset: 150,
            child: FadeTransition(
              opacity: _animationController,
              child: CustomScrollView(
                physics: const BouncingScrollPhysics(),
                slivers: [
                  // 2. Modern Header
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.fromLTRB(28, 80, 28, 32),
                      child: Row(
                        children: [
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  _getGreeting(),
                                  style: const TextStyle(
                                    color: AppTheme.textSub,
                                    fontSize: 14,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  user?.name ?? 'พนักงาน',
                                  style: const TextStyle(
                                    color: AppTheme.textMain,
                                    fontSize: 26,
                                    fontWeight: FontWeight.w900,
                                    letterSpacing: -0.5,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ),
                          ),
                          _buildNotificationButton(),
                        ],
                      ),
                    ),
                  ),

                  // 3. Status Cards Row / Grid
                  SliverPadding(
                    padding: const EdgeInsets.symmetric(horizontal: 28),
                    sliver: SliverToBoxAdapter(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildSectionTitle('แดชบอร์ดกำลังพล'),
                          const SizedBox(height: 16),
                          _buildBalanceList(leaveProvider),
                        ],
                      ),
                    ),
                  ),

                  // 4. Leave Management Section
                  SliverPadding(
                    padding: const EdgeInsets.fromLTRB(28, 32, 28, 8),
                    sliver: SliverToBoxAdapter(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildSectionTitle('การจัดการการลา'),
                          const SizedBox(height: 16),
                          Row(
                            children: [
                              Expanded(
                                child: _buildActionCard(
                                  title: 'ลางาน',
                                  subtitle: 'เขียนใบลา',
                                  icon: Icons.add_rounded,
                                  color: AppTheme.primary,
                                  onTap: () => Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder: (_) =>
                                          const LeaveRequestScreen(),
                                    ),
                                  ),
                                ),
                              ),
                              const SizedBox(width: 16),
                              Expanded(
                                child: _buildActionCard(
                                  title: 'ประวัติ',
                                  subtitle: 'ดูการลาย้อนหลัง',
                                  icon: Icons.history_rounded,
                                  color: AppTheme.secondary,
                                  onTap: () => Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder: (_) =>
                                          const LeaveHistoryScreen(),
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),

                  // 5. Guard Change Section
                  SliverPadding(
                    padding: const EdgeInsets.fromLTRB(28, 24, 28, 40),
                    sliver: SliverToBoxAdapter(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildSectionTitle('การเปลี่ยนเวรยาม'),
                          const SizedBox(height: 16),
                          Row(
                            children: [
                              Expanded(
                                child: _buildActionCard(
                                  title: 'ขอเปลี่ยนยาม',
                                  subtitle: 'ส่งคำขอใหม่',
                                  icon: Icons.swap_horiz_rounded,
                                  color: AppTheme.accent,
                                  onTap: () => Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder: (_) =>
                                          const GuardChangeRequestScreen(),
                                    ),
                                  ),
                                ),
                              ),
                              const SizedBox(width: 16),
                              Expanded(
                                child: _buildActionCard(
                                  title: 'รายการ',
                                  subtitle: 'ประวัติการเปลี่ยน',
                                  icon: Icons.assignment_rounded,
                                  color: Colors.teal,
                                  onTap: () => Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder: (_) =>
                                          const GuardChangeHistoryScreen(),
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 16),
                          _buildLongActionCard(
                            title: 'คำขอเปลี่ยนยามถึงฉัน',
                            subtitle: 'ตรวจสอบและดำเนินการยืนยัน',
                            icon: Icons.notification_important_rounded,
                            color: Colors.indigo,
                            onTap: () => Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) =>
                                    const GuardChangeApprovalsScreen(),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),

                  // 6. Recent Activities
                  SliverPadding(
                    padding: const EdgeInsets.symmetric(horizontal: 28),
                    sliver: SliverToBoxAdapter(
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          _buildSectionTitle('คำขอล่าสุด'),
                          TextButton(
                            onPressed: () => Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => const LeaveHistoryScreen(),
                              ),
                            ),
                            child: const Text(
                              'ดูทั้งหมด',
                              style: TextStyle(
                                fontWeight: FontWeight.w700,
                                color: AppTheme.primary,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),

                  SliverPadding(
                    padding: const EdgeInsets.only(bottom: 100),
                    sliver:
                        leaveProvider.isLoading &&
                            leaveProvider.myRequests.isEmpty
                        ? const SliverToBoxAdapter(
                            child: Center(
                              child: Padding(
                                padding: EdgeInsets.all(40),
                                child: CircularProgressIndicator(),
                              ),
                            ),
                          )
                        : leaveProvider.myRequests.isEmpty
                        ? _buildEmptyRequests()
                        : SliverList(
                            delegate: SliverChildBuilderDelegate(
                              (context, index) {
                                if (index >= leaveProvider.myRequests.length ||
                                    index > 2)
                                  return null;
                                return _buildRecentRequestItem(
                                  leaveProvider.myRequests[index],
                                );
                              },
                              childCount: leaveProvider.myRequests.length > 3
                                  ? 3
                                  : leaveProvider.myRequests.length,
                            ),
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

  Widget _buildFloatingCircle({
    required double size,
    required Color color,
    double? top,
    double? bottom,
    double? left,
    double? right,
  }) {
    return Positioned(
      top: top,
      bottom: bottom,
      left: left,
      right: right,
      child: Container(
        width: size,
        height: size,
        decoration: BoxDecoration(shape: BoxShape.circle, color: color),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(
      title,
      style: const TextStyle(
        fontSize: 18,
        fontWeight: FontWeight.w900,
        color: AppTheme.textMain,
        letterSpacing: -0.5,
      ),
    );
  }

  Widget _buildNotificationButton() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
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
            onPressed: () {},
            icon: const Icon(
              Icons.notifications_none_rounded,
              color: AppTheme.textMain,
            ),
          ),
          Positioned(
            right: 12,
            top: 12,
            child: Container(
              width: 8,
              height: 8,
              decoration: BoxDecoration(
                color: AppTheme.error,
                shape: BoxShape.circle,
                border: Border.all(color: Colors.white, width: 1.5),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBalanceList(LeaveProvider provider) {
    if (provider.isLoading && provider.balances.isEmpty) {
      return const SizedBox(
        height: 100,
        child: Center(child: CircularProgressIndicator()),
      );
    }
    if (provider.balances.isEmpty) {
      return Container(
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: AppTheme.border.withOpacity(0.5)),
        ),
        child: const Center(child: Text('ไม่พบข้อมูลสิทธิ์คงเหลือ')),
      );
    }

    return SizedBox(
      height: 140,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        physics: const BouncingScrollPhysics(),
        itemCount: provider.balances.length,
        itemBuilder: (context, index) {
          final bal = provider.balances[index];
          final color = _getLeaveTypeColor(bal.leaveType?.slug ?? '');
          return Container(
            width: 140,
            margin: const EdgeInsets.only(right: 16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(28),
              boxShadow: [
                BoxShadow(
                  color: color.withOpacity(0.1),
                  blurRadius: 20,
                  offset: const Offset(0, 8),
                ),
              ],
            ),
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: color.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Icon(
                      _getLeaveTypeIcon(bal.leaveType?.slug ?? ''),
                      color: color,
                      size: 18,
                    ),
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        '${bal.remainingDays.toString().replaceAll(RegExp(r'\.0$'), '')} วัน',
                        style: const TextStyle(
                          fontWeight: FontWeight.w900,
                          fontSize: 18,
                          color: AppTheme.textMain,
                        ),
                      ),
                      Text(
                        bal.leaveType?.name ?? 'ไม่ระบุ',
                        style: const TextStyle(
                          color: AppTheme.textSub,
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildActionCard({
    required String title,
    required String subtitle,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(28),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.03),
              blurRadius: 20,
              offset: const Offset(0, 10),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 24),
            ),
            const SizedBox(height: 16),
            Text(
              title,
              style: const TextStyle(
                fontWeight: FontWeight.w900,
                fontSize: 16,
                color: AppTheme.textMain,
              ),
            ),
            Text(
              subtitle,
              style: const TextStyle(
                color: AppTheme.textSub,
                fontSize: 11,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLongActionCard({
    required String title,
    required String subtitle,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(28),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.03),
              blurRadius: 20,
              offset: const Offset(0, 10),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 24),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      fontWeight: FontWeight.w900,
                      fontSize: 15,
                      color: AppTheme.textMain,
                    ),
                  ),
                  Text(
                    subtitle,
                    style: const TextStyle(
                      color: AppTheme.textSub,
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
            const Icon(Icons.chevron_right_rounded, color: AppTheme.textSub),
          ],
        ),
      ),
    );
  }

  Widget _buildRecentRequestItem(dynamic request) {
    final statusColor = _getStatusColor(request.status);
    return Container(
      margin: const EdgeInsets.fromLTRB(28, 0, 28, 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: AppTheme.border.withOpacity(0.3)),
      ),
      child: Row(
        children: [
          Container(
            width: 50,
            height: 50,
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Icon(
              _getLeaveTypeIcon(request.leaveType?.slug ?? ''),
              color: statusColor,
              size: 24,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  request.leaveType?.name ?? 'ไม่ระบุ',
                  style: const TextStyle(
                    fontWeight: FontWeight.w800,
                    fontSize: 15,
                  ),
                ),
                Text(
                  '${request.formattedStartDate} - ${request.formattedEndDate}',
                  style: const TextStyle(color: AppTheme.textSub, fontSize: 12),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Text(
              _getStatusText(request.status),
              style: TextStyle(
                color: statusColor,
                fontSize: 10,
                fontWeight: FontWeight.w900,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyRequests() {
    return const SliverToBoxAdapter(
      child: Center(
        child: Padding(
          padding: EdgeInsets.all(40),
          child: Text(
            'ไม่มีประวัติการลาล่าสุด',
            style: TextStyle(color: AppTheme.textSub, fontSize: 14),
          ),
        ),
      ),
    );
  }

  Color _getLeaveTypeColor(String slug) {
    if (slug == 'vacation') return AppTheme.primary;
    if (slug == 'sick') return Colors.redAccent;
    return Colors.amber;
  }

  IconData _getLeaveTypeIcon(String slug) {
    if (slug == 'vacation') return Icons.beach_access_rounded;
    if (slug == 'sick') return Icons.medication_rounded;
    return Icons.business_center_rounded;
  }

  Color _getStatusColor(String status) {
    if (status == 'approved') return AppTheme.success;
    if (status == 'rejected') return AppTheme.error;
    return AppTheme.warning;
  }

  String _getStatusText(String status) {
    if (status == 'approved') return 'อนุมัติ';
    if (status == 'rejected') return 'ปฏิเสธ';
    return 'รออนุมัติ';
  }
}
