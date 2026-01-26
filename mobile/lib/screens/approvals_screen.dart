import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../models/leave_request_model.dart';
import '../providers/leave_provider.dart';
import '../providers/auth_provider.dart';
import '../widgets/signature_dialog.dart';
import 'leave_report_screen.dart';
import 'guard_report_screen.dart';

class ApprovalsScreen extends StatefulWidget {
  const ApprovalsScreen({super.key});

  @override
  State<ApprovalsScreen> createState() => _ApprovalsScreenState();
}

class _ApprovalsScreenState extends State<ApprovalsScreen>
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
      Provider.of<LeaveProvider>(
        context,
        listen: false,
      ).fetchPendingApprovals();
    });
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  Future<void> _handleApproval(int id, bool isApprove) async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final user = authProvider.user;

    final result = await showDialog<Map<String, dynamic>>(
      context: context,
      builder: (ctx) => SignatureDialog(
        isApprove: isApprove,
        savedSignatureUrl: user?.signatureUrl,
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
      final comment = result['comment'] as String?;
      final signature = result['signature'] as String?;
      final useSavedSignature = result['useSavedSignature'] as bool? ?? false;

      final Map<String, dynamic> response = isApprove
          ? await provider.approveRequest(
              id,
              comment: comment,
              signature: signature,
              useSavedSignature: useSavedSignature,
            )
          : await provider.rejectRequest(id, comment: comment);

      if (!mounted) return;
      Navigator.pop(context);

      final bool isSuccess = response['success'] == true;
      final String message =
          response['message'] ??
          (isSuccess ? 'ทำรายการสำเร็จ' : 'เกิดข้อผิดพลาด');

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            message,
            style: const TextStyle(fontWeight: FontWeight.w600),
          ),
          backgroundColor: isSuccess ? AppTheme.success : AppTheme.error,
          behavior: SnackBarBehavior.floating,
          margin: const EdgeInsets.all(24),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
          ),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final leaveProvider = Provider.of<LeaveProvider>(context);
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: Colors.white,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text(
          'การอนุมัติ',
          style: TextStyle(fontWeight: FontWeight.w900),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: AppTheme.textMain,
        elevation: 0,
      ),
      body: Stack(
        children: [
          Container(
            height: size.height,
            width: size.width,
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [
                  Color(0xFFF8FAFC),
                  Colors.white,
                  Color(0xFFFFF1F2),
                ], // Slight rose hint
              ),
            ),
          ),
          _buildFloatingCircle(
            top: -40,
            right: -40,
            size: 220,
            color: const Color(0xFFF43F5E).withOpacity(0.05),
          ),
          _buildFloatingCircle(
            bottom: -50,
            left: -50,
            size: 280,
            color: Colors.indigo.withOpacity(0.04),
          ),

          SafeArea(
            child: RefreshIndicator(
              onRefresh: () => leaveProvider.fetchPendingApprovals(),
              color: AppTheme.primary,
              child: FadeTransition(
                opacity: _animationController,
                child: _buildContent(leaveProvider),
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

  Widget _buildContent(LeaveProvider provider) {
    if (provider.isLoading && provider.pendingApprovals.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    return CustomScrollView(
      physics: const BouncingScrollPhysics(),
      slivers: [
        SliverToBoxAdapter(child: _buildReportSection()),
        SliverPadding(
          padding: const EdgeInsets.fromLTRB(28, 16, 28, 16),
          sliver: SliverToBoxAdapter(
            child: Text(
              'รอการพิจารณา (${provider.pendingApprovals.length})',
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w900,
                color: AppTheme.textMain,
              ),
            ),
          ),
        ),
        if (provider.pendingApprovals.isEmpty)
          SliverFillRemaining(
            hasScrollBody: false,
            child: Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    padding: const EdgeInsets.all(32),
                    decoration: BoxDecoration(
                      color: AppTheme.border.withOpacity(0.2),
                      shape: BoxShape.circle,
                    ),
                    child: Icon(
                      Icons.fact_check_rounded,
                      size: 64,
                      color: AppTheme.textSub.withOpacity(0.2),
                    ),
                  ),
                  const SizedBox(height: 24),
                  const Text(
                    'ไม่มีรายการรออนุมัติ',
                    style: TextStyle(
                      color: AppTheme.textSub,
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ],
              ),
            ),
          )
        else
          SliverList(
            delegate: SliverChildBuilderDelegate((context, index) {
              final request = provider.pendingApprovals[index];
              return _buildApprovalCard(request);
            }, childCount: provider.pendingApprovals.length),
          ),
        const SliverPadding(padding: EdgeInsets.only(bottom: 120)),
      ],
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
      padding: const EdgeInsets.fromLTRB(28, 24, 28, 8),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'รายงานระบบ',
            style: TextStyle(
              fontSize: 22,
              fontWeight: FontWeight.w900,
              color: AppTheme.textMain,
              letterSpacing: -0.5,
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
        padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
        decoration: BoxDecoration(
          color: color.withOpacity(0.06),
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: color.withOpacity(0.1)),
        ),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white,
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(color: color.withOpacity(0.1), blurRadius: 10),
                ],
              ),
              child: Icon(icon, color: color, size: 28),
            ),
            const SizedBox(height: 12),
            Text(
              label,
              style: TextStyle(
                color: color,
                fontWeight: FontWeight.w900,
                fontSize: 15,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildApprovalCard(LeaveRequest request) {
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
                            style: const TextStyle(
                              fontWeight: FontWeight.w900,
                              fontSize: 18,
                              color: AppTheme.textMain,
                            ),
                          ),
                          Text(
                            request.user?.position ?? 'ไม่ระบุตำแหน่ง',
                            style: const TextStyle(
                              color: AppTheme.textSub,
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
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
                    const SizedBox(height: 16),
                    Text(
                      'ประเภท: ${request.leaveType.name}',
                      style: const TextStyle(
                        fontWeight: FontWeight.w800,
                        fontSize: 14,
                        color: AppTheme.textMain,
                      ),
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
                          style: const TextStyle(
                            color: AppTheme.textSub,
                            fontSize: 14,
                            height: 1.4,
                            fontWeight: FontWeight.w500,
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
                        onPressed: () => _handleApproval(request.id, false),
                        style: TextButton.styleFrom(
                          foregroundColor: AppTheme.error,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                        child: const Text(
                          'ปฏิเสธ',
                          style: TextStyle(
                            fontWeight: FontWeight.w900,
                            fontSize: 15,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(16),
                          gradient: const LinearGradient(
                            colors: [AppTheme.primary, AppTheme.secondary],
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: AppTheme.primary.withOpacity(0.2),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: ElevatedButton(
                          onPressed: () => _handleApproval(request.id, true),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.transparent,
                            foregroundColor: Colors.white,
                            shadowColor: Colors.transparent,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                          ),
                          child: const Text(
                            'อนุมัติ',
                            style: TextStyle(
                              fontWeight: FontWeight.w900,
                              fontSize: 15,
                            ),
                          ),
                        ),
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

  Widget _buildBadgeInfo(IconData icon, String text) {
    return Row(
      children: [
        Icon(icon, size: 14, color: AppTheme.textSub),
        const SizedBox(width: 8),
        Text(
          text,
          style: const TextStyle(
            fontWeight: FontWeight.w800,
            fontSize: 13,
            color: AppTheme.textMain,
          ),
        ),
      ],
    );
  }

  IconData _getLeaveTypeIcon(String slug) {
    if (slug == 'vacation') return Icons.beach_access_rounded;
    if (slug == 'sick') return Icons.medication_rounded;
    if (slug == 'personal') return Icons.business_center_rounded;
    return Icons.event_note_rounded;
  }
}
