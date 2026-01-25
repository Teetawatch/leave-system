import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
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

class _ApprovalsScreenState extends State<ApprovalsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<LeaveProvider>(
        context,
        listen: false,
      ).fetchPendingApprovals();
    });
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
      final provider = Provider.of<LeaveProvider>(context, listen: false);
      final comment = result['comment'] as String?;
      final signature = result['signature'] as String?;
      final useSavedSignature = result['useSavedSignature'] as bool? ?? false;

      final success = isApprove
          ? await provider.approveRequest(
              id,
              comment: comment,
              signature: signature,
              useSavedSignature: useSavedSignature,
            )
          : await provider.rejectRequest(id, comment: comment);

      if (mounted) {
        if (success) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                isApprove ? 'อนุมัติเรียบร้อยแล้ว' : 'ปฏิเสธเรียบร้อยแล้ว',
              ),
              backgroundColor: isApprove ? AppTheme.success : AppTheme.error,
              behavior: SnackBarBehavior.floating,
              margin: const EdgeInsets.all(24),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
            ),
          );
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final leaveProvider = Provider.of<LeaveProvider>(context);

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('รายการรออนุมัติ'),
        backgroundColor: Colors.transparent,
        surfaceTintColor: Colors.transparent,
      ),
      body: RefreshIndicator(
        onRefresh: () => leaveProvider.fetchPendingApprovals(),
        color: AppTheme.primary,
        child: _buildContent(leaveProvider),
      ),
    );
  }

  Widget _buildContent(LeaveProvider provider) {
    if (provider.isLoading && provider.pendingApprovals.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    return CustomScrollView(
      slivers: [
        SliverToBoxAdapter(child: _buildReportSection()),
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
                      Icons.fact_check_outlined,
                      size: 64,
                      color: AppTheme.textSub.withOpacity(0.2),
                    ),
                  ),
                  const SizedBox(height: 24),
                  Text(
                    'ไม่มีรายการรออนุมัติ',
                    style: TextStyle(
                      color: AppTheme.textSub.withOpacity(0.5),
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ],
              ),
            ),
          )
        else ...[
          SliverPadding(
            padding: const EdgeInsets.fromLTRB(24, 16, 24, 8),
            sliver: SliverToBoxAdapter(
              child: Text(
                'รอการพิจารณา (${provider.pendingApprovals.length})',
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                  fontWeight: FontWeight.w800,
                  letterSpacing: -0.5,
                ),
              ),
            ),
          ),
          SliverList(
            delegate: SliverChildBuilderDelegate((context, index) {
              final request = provider.pendingApprovals[index];
              return _buildApprovalCard(request);
            }, childCount: provider.pendingApprovals.length),
          ),
        ],
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
      padding: const EdgeInsets.fromLTRB(24, 16, 24, 8),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'รายงานระบบ',
            style: Theme.of(context).textTheme.titleLarge?.copyWith(
              fontWeight: FontWeight.w800,
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
              const SizedBox(width: 12),
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
          const SizedBox(height: 16),
          const Divider(color: AppTheme.border),
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
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 12),
        decoration: BoxDecoration(
          color: color.withOpacity(0.08),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: color.withOpacity(0.15)),
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 28),
            const SizedBox(height: 8),
            Text(
              label,
              style: TextStyle(
                color: color,
                fontWeight: FontWeight.bold,
                fontSize: 14,
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
        border: Border.all(color: AppTheme.border.withOpacity(0.5)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // User Info Header
          Padding(
            padding: const EdgeInsets.all(24),
            child: Row(
              children: [
                Container(
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    border: Border.all(
                      color: AppTheme.primary.withOpacity(0.2),
                      width: 2,
                    ),
                  ),
                  child: CircleAvatar(
                    radius: 28,
                    backgroundColor: AppTheme.primary.withOpacity(0.1),
                    backgroundImage: request.user?.avatarUrl != null
                        ? NetworkImage(request.user!.avatarUrl!)
                        : null,
                    child: request.user?.avatarUrl == null
                        ? const Icon(
                            Icons.person,
                            color: AppTheme.primary,
                            size: 28,
                          )
                        : null,
                  ),
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
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 10,
                    vertical: 6,
                  ),
                  decoration: BoxDecoration(
                    color: AppTheme.primary.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Icon(
                    _getLeaveTypeIcon(request.leaveType.slug),
                    color: AppTheme.primary,
                    size: 18,
                  ),
                ),
              ],
            ),
          ),

          const Divider(height: 1, color: AppTheme.border),

          // Request Details
          Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    _buildCompactInfo(
                      Icons.calendar_today_rounded,
                      '${request.formattedStartDate} - ${request.formattedEndDate}',
                    ),
                    const Spacer(),
                    _buildCompactInfo(
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
                      color: AppTheme.background,
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'เหตุผลการลา:',
                          style: TextStyle(
                            color: AppTheme.textSub,
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          request.reason,
                          style: const TextStyle(
                            color: AppTheme.textMain,
                            fontSize: 14,
                            height: 1.4,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ],
            ),
          ),

          // Action Buttons
          Padding(
            padding: const EdgeInsets.fromLTRB(24, 0, 24, 24),
            child: Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: () => _handleApproval(request.id, false),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: AppTheme.error,
                      side: const BorderSide(color: AppTheme.error, width: 1.5),
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(18),
                      ),
                    ),
                    child: const Text(
                      'ปฏิเสธ',
                      style: TextStyle(fontWeight: FontWeight.w800),
                    ),
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: ElevatedButton(
                    onPressed: () => _handleApproval(request.id, true),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.success,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(18),
                      ),
                      elevation: 4,
                      shadowColor: AppTheme.success.withOpacity(0.3),
                    ),
                    child: const Text(
                      'อนุมัติ',
                      style: TextStyle(fontWeight: FontWeight.w800),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCompactInfo(IconData icon, String text) {
    return Row(
      children: [
        Icon(icon, size: 14, color: AppTheme.textSub),
        const SizedBox(width: 6),
        Text(
          text,
          style: const TextStyle(
            fontWeight: FontWeight.w700,
            fontSize: 13,
            color: AppTheme.textMain,
          ),
        ),
      ],
    );
  }

  IconData _getLeaveTypeIcon(String slug) {
    switch (slug) {
      case 'vacation':
        return Icons.beach_access_rounded;
      case 'sick':
        return Icons.medication_rounded;
      case 'personal':
        return Icons.business_center_rounded;
      default:
        return Icons.event_note_rounded;
    }
  }
}
