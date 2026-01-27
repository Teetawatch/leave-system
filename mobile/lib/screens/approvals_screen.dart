import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../models/leave_request_model.dart';
import '../models/guard_change_model.dart';
import '../providers/leave_provider.dart';
import '../providers/guard_change_provider.dart';
import '../providers/auth_provider.dart';
import '../widgets/signature_dialog.dart';
import 'leave_report_screen.dart';
import 'guard_report_screen.dart';
import 'package:google_fonts/google_fonts.dart';
import '../widgets/animated_background.dart';

class ApprovalsScreen extends StatefulWidget {
  const ApprovalsScreen({super.key});

  @override
  State<ApprovalsScreen> createState() => _ApprovalsScreenState();
}

class _ApprovalsScreenState extends State<ApprovalsScreen>
    with TickerProviderStateMixin {
  late TabController _tabController;
  late AnimationController _animationController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1000),
    )..forward();

    WidgetsBinding.instance.addPostFrameCallback((_) {
      _refreshAll();
    });
  }

  void _refreshAll() {
    Provider.of<LeaveProvider>(context, listen: false).fetchPendingApprovals();
    Provider.of<GuardChangeProvider>(context, listen: false).fetchApprovals();
  }

  @override
  void dispose() {
    _tabController.dispose();
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final leaveProvider = Provider.of<LeaveProvider>(context);
    final guardProvider = Provider.of<GuardChangeProvider>(context);

    return Scaffold(
      backgroundColor: Colors.white,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: Text(
          'การอนุมัติ & ยืนยัน',
          style: GoogleFonts.kanit(
            fontWeight: FontWeight.w700,
            fontSize: 22,
            color: AppTheme.textMain,
          ),
        ),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: false,
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(70),
          child: Container(
            margin: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
            padding: const EdgeInsets.all(4),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.8),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: Colors.white.withOpacity(0.5)),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.05),
                  blurRadius: 20,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(16),
              child: BackdropFilter(
                filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
                child: TabBar(
                  controller: _tabController,
                  indicator: BoxDecoration(
                    color: AppTheme.primary,
                    borderRadius: BorderRadius.circular(16),
                    boxShadow: [
                      BoxShadow(
                        color: AppTheme.primary.withOpacity(0.3),
                        blurRadius: 8,
                        offset: const Offset(0, 2),
                      ),
                    ],
                  ),
                  labelColor: Colors.white,
                  unselectedLabelColor: AppTheme.textSub,
                  labelStyle: GoogleFonts.kanit(
                    fontWeight: FontWeight.w600,
                    fontSize: 14,
                  ),
                  unselectedLabelStyle: GoogleFonts.kanit(
                    fontWeight: FontWeight.w500,
                    fontSize: 14,
                  ),
                  indicatorSize: TabBarIndicatorSize.tab,
                  dividerColor: Colors.transparent,
                  overlayColor: MaterialStateProperty.all(Colors.transparent),
                  tabs: [
                    Tab(
                      text: 'การลา (${leaveProvider.pendingApprovals.length})',
                    ),
                    Tab(text: 'เปลี่ยนเวร (${guardProvider.approvals.length})'),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
      body: Stack(
        children: [
          // Animated Background
          const Positioned.fill(child: AnimatedBackground()),

          SafeArea(
            child: FadeTransition(
              opacity: _animationController,
              child: TabBarView(
                controller: _tabController,
                physics: const BouncingScrollPhysics(),
                children: [
                  _buildLeaveApprovals(leaveProvider),
                  _buildGuardApprovals(guardProvider),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  // --- Leave Approvals Implementation ---

  Widget _buildLeaveApprovals(LeaveProvider provider) {
    if (provider.isLoading && provider.pendingApprovals.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    return RefreshIndicator(
      onRefresh: () async => provider.fetchPendingApprovals(),
      child: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          SliverToBoxAdapter(child: _buildReportSection()),
          if (provider.pendingApprovals.isEmpty)
            _buildEmptyState(Icons.fact_check_rounded, 'ไม่มีรายการลารออนุมัติ')
          else
            SliverList(
              delegate: SliverChildBuilderDelegate((context, index) {
                final request = provider.pendingApprovals[index];
                return _buildLeaveApprovalCard(request);
              }, childCount: provider.pendingApprovals.length),
            ),
          const SliverPadding(padding: EdgeInsets.only(bottom: 120)),
        ],
      ),
    );
  }

  Widget _buildReportSection() {
    final user = Provider.of<AuthProvider>(context).user;
    final bool isAdmin =
        user?.role == 'admin' ||
        user?.role == 'director' ||
        user?.role == 'deputy_director' ||
        user?.role == 'department_head';
    if (!isAdmin) return const SizedBox.shrink();

    return Padding(
      padding: const EdgeInsets.fromLTRB(28, 24, 28, 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'รายงานระบบ',
            style: GoogleFonts.kanit(
              fontSize: 20,
              fontWeight: FontWeight.w700,
              color: AppTheme.textMain,
            ),
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: _buildReportButton(
                  'สรุปการลา',
                  Icons.summarize_rounded,
                  AppTheme.primary,
                  () => Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => const LeaveReportScreen(),
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildReportButton(
                  'เปลี่ยนเวร',
                  Icons.security_rounded,
                  AppTheme.secondary,
                  () => Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => const GuardReportScreen(),
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          const Divider(height: 1),
        ],
      ),
    );
  }

  Widget _buildReportButton(
    String label,
    IconData icon,
    Color color,
    VoidCallback onTap,
  ) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(24),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 16),
        decoration: BoxDecoration(
          color: color.withOpacity(0.06),
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: color.withOpacity(0.1)),
        ),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.white,
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 24),
            ),
            const SizedBox(height: 8),
            Text(
              label,
              style: GoogleFonts.kanit(
                color: color,
                fontWeight: FontWeight.w600,
                fontSize: 14,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLeaveApprovalCard(LeaveRequest request) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(32),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 5, sigmaY: 5),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.all(24),
                child: Row(
                  children: [
                    CircleAvatar(
                      radius: 28,
                      backgroundColor: AppTheme.primary.withOpacity(0.1),
                      backgroundImage: request.user?.avatarUrl != null
                          ? NetworkImage(request.user!.avatarUrl!)
                          : null,
                      child: request.user?.avatarUrl == null
                          ? const Icon(
                              Icons.person_rounded,
                              color: AppTheme.primary,
                              size: 32,
                            )
                          : null,
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            request.user?.name ?? 'ไม่ระบุชื่อ',
                            style: GoogleFonts.kanit(
                              fontWeight: FontWeight.w700,
                              fontSize: 18,
                              color: AppTheme.textMain,
                            ),
                          ),
                          Text(
                            request.user?.position ?? 'ไม่ระบุตำแหน่ง',
                            style: GoogleFonts.sarabun(
                              color: AppTheme.textSub,
                              fontSize: 13,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: AppTheme.primary.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: Icon(
                        _getLeaveTypeIcon(request.leaveType.slug),
                        color: AppTheme.primary,
                        size: 20,
                      ),
                    ),
                  ],
                ),
              ),
              const Divider(height: 1),
              Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        _buildBadgeInfo(
                          Icons.calendar_today_rounded,
                          '${request.formattedStartDate} - ${request.formattedEndDate}',
                        ),
                        const Spacer(),
                        _buildBadgeInfo(
                          Icons.timer_outlined,
                          '${request.totalDays} วัน',
                        ),
                      ],
                    ),
                    if (request.reason.isNotEmpty) ...[
                      const SizedBox(height: 12),
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Color(0xFFF1F5F9),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          request.reason,
                          style: GoogleFonts.sarabun(
                            color: AppTheme.textSub,
                            fontSize: 14,
                            height: 1.5,
                          ),
                        ),
                      ),
                    ],
                  ],
                ),
              ),
              Padding(
                padding: const EdgeInsets.fromLTRB(24, 0, 24, 24),
                child: Row(
                  children: [
                    Expanded(
                      child: TextButton(
                        onPressed: () =>
                            _handleLeaveApproval(request.id, false),
                        style: TextButton.styleFrom(
                          foregroundColor: AppTheme.error,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                        child: Text(
                          'ปฏิเสธ',
                          style: GoogleFonts.kanit(
                            fontWeight: FontWeight.w700,
                            fontSize: 16,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: _buildActionButton(
                        'อนุมัติ',
                        Colors.green,
                        () => _handleLeaveApproval(request.id, true),
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

  // --- Guard Change Approvals Implementation ---

  Widget _buildGuardApprovals(GuardChangeProvider provider) {
    if (provider.isLoading && provider.approvals.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    return RefreshIndicator(
      onRefresh: () async => provider.fetchApprovals(),
      child: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          if (provider.approvals.isEmpty)
            _buildEmptyState(
              Icons.security_rounded,
              'ไม่มีคำขอเปลี่ยนเวรรอยืนยัน',
            )
          else
            SliverPadding(
              padding: const EdgeInsets.only(top: 20),
              sliver: SliverList(
                delegate: SliverChildBuilderDelegate((context, index) {
                  final request = provider.approvals[index];
                  return _buildGuardApprovalCard(request);
                }, childCount: provider.approvals.length),
              ),
            ),
          const SliverPadding(padding: EdgeInsets.only(bottom: 120)),
        ],
      ),
    );
  }

  Widget _buildGuardApprovalCard(GuardChangeRequest request) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 25,
            offset: const Offset(0, 12),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(32),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 5, sigmaY: 5),
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    CircleAvatar(
                      radius: 24,
                      backgroundColor: Colors.orange.withOpacity(0.1),
                      child: Text(
                        request.user?.name.substring(0, 1) ?? '?',
                        style: GoogleFonts.kanit(
                          color: Colors.orange,
                          fontWeight: FontWeight.w700,
                          fontSize: 20,
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            request.user?.name ?? 'ไม่ระบุชื่อ',
                            style: GoogleFonts.kanit(
                              fontWeight: FontWeight.w700,
                              fontSize: 18,
                              color: AppTheme.textMain,
                            ),
                          ),
                          Text(
                            'ต้องการให้ท่านปฏิบัติเวรแทน',
                            style: GoogleFonts.sarabun(
                              color: AppTheme.textSub,
                              fontSize: 13,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Color(0xFFF8FAFC),
                    borderRadius: BorderRadius.circular(24),
                    border: Border.all(color: AppTheme.border.withOpacity(0.3)),
                  ),
                  child: Column(
                    children: [
                      _buildDetailRow(
                        'ตำแหน่งเวร',
                        request.dutyPositionThai,
                        Icons.security_rounded,
                      ),
                      const Padding(
                        padding: EdgeInsets.symmetric(vertical: 8),
                        child: Divider(height: 1),
                      ),
                      _buildDetailRow(
                        'วันที่ปฏิบัติ',
                        request.formattedDutyDate,
                        Icons.calendar_today_rounded,
                      ),
                    ],
                  ),
                ),
                if (request.remarks != null && request.remarks!.isNotEmpty) ...[
                  const SizedBox(height: 16),
                  Text(
                    'หมายเหตุ: ${request.remarks}',
                    style: GoogleFonts.sarabun(
                      color: AppTheme.textSub.withOpacity(0.8),
                      fontSize: 14,
                      fontStyle: FontStyle.italic,
                    ),
                  ),
                ],
                const SizedBox(height: 28),
                Row(
                  children: [
                    Expanded(
                      child: TextButton(
                        onPressed: () => _showGuardRejectDialog(request.id),
                        style: TextButton.styleFrom(
                          foregroundColor: AppTheme.error,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                        child: Text(
                          'ปฏิเสธ',
                          style: GoogleFonts.kanit(
                            fontWeight: FontWeight.w700,
                            fontSize: 16,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildActionButton(
                        'ยืนยันรับเวร',
                        Colors.orange,
                        () => _showGuardApproveDialog(request.id),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // --- Helpers & Dialogs ---

  Widget _buildEmptyState(IconData icon, String message) {
    return SliverFillRemaining(
      hasScrollBody: false,
      child: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(32),
              decoration: BoxDecoration(
                color: AppTheme.border.withOpacity(0.15),
                shape: BoxShape.circle,
              ),
              child: Icon(
                icon,
                size: 64,
                color: AppTheme.textSub.withOpacity(0.2),
              ),
            ),
            const SizedBox(height: 24),
            Text(
              message,
              style: GoogleFonts.kanit(
                color: AppTheme.textSub.withOpacity(0.5),
                fontSize: 18,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBadgeInfo(IconData icon, String text) {
    return Row(
      children: [
        Icon(icon, size: 14, color: AppTheme.textSub),
        const SizedBox(width: 8),
        Text(
          text,
          style: GoogleFonts.sarabun(
            fontWeight: FontWeight.w600,
            fontSize: 13,
            color: AppTheme.textMain,
          ),
        ),
      ],
    );
  }

  Widget _buildDetailRow(String label, String value, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 16, color: AppTheme.textSub),
        const SizedBox(width: 12),
        Text(
          label,
          style: GoogleFonts.sarabun(
            color: AppTheme.textSub,
            fontSize: 13,
            fontWeight: FontWeight.w500,
          ),
        ),
        const Spacer(),
        Text(
          value,
          style: GoogleFonts.kanit(
            color: AppTheme.textMain,
            fontSize: 14,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }

  Widget _buildActionButton(String label, Color color, VoidCallback onTap) {
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        gradient: LinearGradient(colors: [color, color.withRed(100)]),
        boxShadow: [
          BoxShadow(
            color: color.withOpacity(0.2),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ElevatedButton(
        onPressed: onTap,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.transparent,
          foregroundColor: Colors.white,
          shadowColor: Colors.transparent,
          padding: const EdgeInsets.symmetric(vertical: 16),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
        ),
        child: Text(
          label,
          style: GoogleFonts.kanit(fontWeight: FontWeight.w700, fontSize: 16),
        ),
      ),
    );
  }

  IconData _getLeaveTypeIcon(String slug) {
    if (slug == 'vacation') return Icons.beach_access_rounded;
    if (slug == 'sick') return Icons.medication_rounded;
    return Icons.business_center_rounded;
  }

  // --- Logic Implementation ---

  Future<void> _handleLeaveApproval(int id, bool isApprove) async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final result = await showDialog<Map<String, dynamic>>(
      context: context,
      builder: (ctx) => SignatureDialog(
        isApprove: isApprove,
        savedSignatureUrl: authProvider.user?.signatureUrl,
      ),
    );

    if (result != null) {
      if (!mounted) return;
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (ctx) => const Center(child: CircularProgressIndicator()),
      );
      final provider = Provider.of<LeaveProvider>(context, listen: false);
      final response = isApprove
          ? await provider.approveRequest(
              id,
              comment: result['comment'],
              signature: result['signature'],
              useSavedSignature: result['useSavedSignature'] ?? false,
            )
          : await provider.rejectRequest(id, comment: result['comment']);
      if (!mounted) return;
      Navigator.pop(context);
      final bool isSuccess = response['success'] == true;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(response['message'] ?? 'ทำรายการสำเร็จ'),
          backgroundColor: isSuccess ? AppTheme.success : AppTheme.error,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
          ),
        ),
      );
    }
  }

  void _showGuardApproveDialog(int id) {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    showDialog(
      context: context,
      builder: (context) => SignatureDialog(
        isApprove: true,
        savedSignatureUrl: authProvider.user?.signatureUrl,
      ),
    ).then((result) async {
      if (result != null) {
        final provider = Provider.of<GuardChangeProvider>(
          context,
          listen: false,
        );
        final success = await provider.approveRequest(
          id,
          comment: result['comment'],
          signature: result['signature'],
          useSavedSignature: result['useSavedSignature'] ?? false,
        );
        if (success && mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: const Text('ยืนยันการรับเปลี่ยนเวรเรียบร้อยแล้ว'),
              backgroundColor: AppTheme.success,
              behavior: SnackBarBehavior.floating,
            ),
          );
        }
      }
    });
  }

  void _showGuardRejectDialog(int id) {
    final controller = TextEditingController();
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text(
          'ระบุเหตุผลที่ปฏิเสธ',
          style: TextStyle(fontWeight: FontWeight.w900),
        ),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        content: TextField(
          controller: controller,
          decoration: const InputDecoration(hintText: 'ระบุเหตุผล...'),
          maxLines: 3,
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('ยกเลิก'),
          ),
          ElevatedButton(
            onPressed: () async {
              if (controller.text.isEmpty) return;
              final success = await Provider.of<GuardChangeProvider>(
                context,
                listen: false,
              ).rejectRequest(id, comment: controller.text);
              if (success && mounted) {
                Navigator.pop(context);
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: const Text('ปฏิเสธคำขอเรียบร้อยแล้ว'),
                    backgroundColor: AppTheme.error,
                    behavior: SnackBarBehavior.floating,
                  ),
                );
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.error,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
            child: const Text('ยืนยันการปฏิเสธ'),
          ),
        ],
      ),
    );
  }
}
