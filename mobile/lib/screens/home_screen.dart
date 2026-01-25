import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/app_theme.dart';
import '../providers/auth_provider.dart';
import '../providers/leave_provider.dart';
import 'leave_request_screen.dart';
import 'leave_history_screen.dart';
import 'approvals_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  void initState() {
    super.initState();
    // Fetch data on load
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

  Future<void> _refreshData() async {
    final leaveProvider = Provider.of<LeaveProvider>(context, listen: false);
    await Future.wait([
      leaveProvider.fetchLeaveBalances(),
      leaveProvider.fetchMyRequests(),
    ]);
  }

  @override
  Widget build(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).user;
    final leaveProvider = Provider.of<LeaveProvider>(context);

    return Scaffold(
      backgroundColor: AppTheme.background,
      body: RefreshIndicator(
        onRefresh: _refreshData,
        color: AppTheme.primary,
        child: CustomScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          slivers: [
            // Modern Header
            SliverToBoxAdapter(
              child: Container(
                padding: const EdgeInsets.only(
                  left: 24,
                  right: 24,
                  top: 60,
                  bottom: 20,
                ),
                decoration: const BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.only(
                    bottomLeft: Radius.circular(32),
                    bottomRight: Radius.circular(32),
                  ),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          children: [
                            Container(
                              width: 50,
                              height: 50,
                              decoration: BoxDecoration(
                                color: AppTheme.primaryLight,
                                borderRadius: BorderRadius.circular(15),
                                image: user?.avatarUrl != null
                                    ? DecorationImage(
                                        image: NetworkImage(user!.avatarUrl!),
                                        fit: BoxFit.cover,
                                      )
                                    : null,
                              ),
                              child: user?.avatarUrl == null
                                  ? const Icon(
                                      Icons.person_rounded,
                                      color: AppTheme.primary,
                                    )
                                  : null,
                            ),
                            const SizedBox(width: 14),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'สวัสดี 👋',
                                  style: Theme.of(context).textTheme.bodyMedium,
                                ),
                                Text(
                                  user?.name ?? 'พนักงาน',
                                  style: Theme.of(context).textTheme.titleLarge,
                                ),
                              ],
                            ),
                          ],
                        ),
                        Container(
                          decoration: BoxDecoration(
                            color: AppTheme.background,
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: IconButton(
                            onPressed: () => Provider.of<AuthProvider>(
                              context,
                              listen: false,
                            ).logout(),
                            icon: const Icon(
                              Icons.logout_rounded,
                              color: AppTheme.error,
                              size: 22,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 24),
                    Text(
                      'สรุปวันลาคงเหลือ',
                      style: Theme.of(context).textTheme.titleMedium,
                    ),
                    const SizedBox(height: 16),
                    _buildBalanceSection(leaveProvider),
                  ],
                ),
              ),
            ),

            // Quick Actions Title
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(24, 32, 24, 16),
              sliver: SliverToBoxAdapter(
                child: Text(
                  'จัดการการลางาน',
                  style: Theme.of(context).textTheme.titleMedium,
                ),
              ),
            ),

            // Navigation Grid
            SliverPadding(
              padding: const EdgeInsets.symmetric(horizontal: 24),
              sliver: SliverGrid.count(
                crossAxisCount: 2,
                mainAxisSpacing: 16,
                crossAxisSpacing: 16,
                childAspectRatio: 1.1,
                children: [
                  _buildActionCard(
                    context,
                    title: 'ยื่นใบลา',
                    icon: Icons.add_box_rounded,
                    color: AppTheme.primary,
                    onTap: () => Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => const LeaveRequestScreen(),
                      ),
                    ),
                  ),
                  _buildActionCard(
                    context,
                    title: 'ประวัติการลา',
                    icon: Icons.history_rounded,
                    color: AppTheme.secondary,
                    onTap: () => Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => const LeaveHistoryScreen(),
                      ),
                    ),
                  ),
                  if (user?.role == 'supervisor' ||
                      user?.role == 'manager' ||
                      user?.role == 'admin')
                    _buildActionCard(
                      context,
                      title: 'รออนุมัติ',
                      icon: Icons.fact_check_rounded,
                      color: AppTheme.success,
                      badge: leaveProvider.pendingApprovals.length,
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => const ApprovalsScreen(),
                        ),
                      ),
                    ),
                  _buildActionCard(
                    context,
                    title: 'โปรไฟล์',
                    icon: Icons.person_pin_rounded,
                    color: AppTheme.accent,
                    onTap: () {}, // TODO
                  ),
                ],
              ),
            ),

            // Recent Leaves
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(24, 32, 24, 16),
              sliver: SliverToBoxAdapter(
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'รายการล่าสุด',
                      style: Theme.of(context).textTheme.titleMedium,
                    ),
                    TextButton(
                      onPressed: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => const LeaveHistoryScreen(),
                        ),
                      ),
                      child: const Text('ดูทั้งหมด'),
                    ),
                  ],
                ),
              ),
            ),

            SliverPadding(
              padding: const EdgeInsets.only(bottom: 32),
              sliver: leaveProvider.myRequests.isEmpty
                  ? SliverToBoxAdapter(
                      child: Center(
                        child: Padding(
                          padding: const EdgeInsets.all(40),
                          child: Column(
                            children: [
                              Icon(
                                Icons.inbox_rounded,
                                size: 48,
                                color: Colors.grey[300],
                              ),
                              const SizedBox(height: 12),
                              Text(
                                'ยังไม่มีรายการลา',
                                style: TextStyle(color: Colors.grey[400]),
                              ),
                            ],
                          ),
                        ),
                      ),
                    )
                  : SliverList(
                      delegate: SliverChildBuilderDelegate(
                        (context, index) {
                          final request = leaveProvider.myRequests[index];
                          if (index > 2) return null; // Only show top 3
                          return _buildRecentRequestItem(request);
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
    );
  }

  Widget _buildBalanceSection(LeaveProvider provider) {
    if (provider.isLoading && provider.balances.isEmpty) {
      return const SizedBox(
        height: 100,
        child: Center(child: CircularProgressIndicator()),
      );
    }

    if (provider.balances.isEmpty) {
      return Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: AppTheme.background,
          borderRadius: BorderRadius.circular(16),
        ),
        child: const Center(child: Text('ไม่พบข้อมูลวันคงเหลือ')),
      );
    }

    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      clipBehavior: Clip.none,
      child: Row(
        children: provider.balances.map((bal) {
          return Container(
            width: 150,
            margin: const EdgeInsets.only(right: 16),
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [AppTheme.primary, AppTheme.primaryDark],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(24),
              boxShadow: [
                BoxShadow(
                  color: AppTheme.primary.withOpacity(0.3),
                  blurRadius: 12,
                  offset: const Offset(0, 6),
                ),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  bal.leaveType?.name ?? 'ไม่ระบุ',
                  style: const TextStyle(color: Colors.white70, fontSize: 12),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 8),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.baseline,
                  textBaseline: TextBaseline.alphabetic,
                  children: [
                    Text(
                      bal.remainingDays.toString().replaceAll(
                        RegExp(r'\.0$'),
                        '',
                      ),
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 28,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(width: 4),
                    const Text(
                      'วัน',
                      style: TextStyle(color: Colors.white, fontSize: 14),
                    ),
                  ],
                ),
                const SizedBox(height: 4),
                Text(
                  'จากทั้งหมด ${bal.totalDays.toInt()} วัน',
                  style: const TextStyle(color: Colors.white60, fontSize: 10),
                ),
              ],
            ),
          );
        }).toList(),
      ),
    );
  }

  Widget _buildActionCard(
    BuildContext context, {
    required String title,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
    int badge = 0,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: AppTheme.border, width: 1),
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
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: color.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Icon(icon, color: color, size: 24),
                    ),
                    if (badge > 0)
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 4,
                        ),
                        decoration: BoxDecoration(
                          color: AppTheme.error,
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          badge.toString(),
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                  ],
                ),
                Text(
                  title,
                  style: Theme.of(
                    context,
                  ).textTheme.labelLarge?.copyWith(fontSize: 16),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRecentRequestItem(dynamic request) {
    final statusColor = _getStatusColor(request.status);

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 24, vertical: 6),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.border, width: 1),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(
              _getStatusIcon(request.status),
              color: statusColor,
              size: 20,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  request.leaveType?.name ?? 'ลางาน',
                  style: const TextStyle(fontWeight: FontWeight.bold),
                ),
                Text(
                  '${request.formattedStartDate} - ${request.formattedEndDate}',
                  style: Theme.of(context).textTheme.bodyMedium,
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Text(
              _getStatusText(request.status),
              style: TextStyle(
                color: statusColor,
                fontSize: 12,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'approved':
        return AppTheme.success;
      case 'pending':
        return AppTheme.warning;
      case 'rejected':
        return AppTheme.error;
      case 'cancelled':
        return Colors.grey;
      default:
        return AppTheme.primary;
    }
  }

  IconData _getStatusIcon(String status) {
    switch (status) {
      case 'approved':
        return Icons.check_circle_rounded;
      case 'pending':
        return Icons.pending_rounded;
      case 'rejected':
        return Icons.cancel_rounded;
      case 'cancelled':
        return Icons.block_rounded;
      default:
        return Icons.info_rounded;
    }
  }

  String _getStatusText(String status) {
    switch (status) {
      case 'approved':
        return 'อนุมัติแล้ว';
      case 'pending':
        return 'รออนุมัติ';
      case 'rejected':
        return 'ปฏิเสธ';
      case 'cancelled':
        return 'ยกเลิก';
      default:
        return status;
    }
  }
}
