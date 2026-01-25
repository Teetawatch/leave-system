import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/app_theme.dart';
import '../models/leave_request_model.dart';
import '../providers/leave_provider.dart';

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
    final commentController = TextEditingController();

    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text(isApprove ? 'ยืนยันการอนุมัติ' : 'ระบุเหตุผลการปฏิเสธ'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: commentController,
              decoration: InputDecoration(
                hintText: isApprove
                    ? 'ความคิดเห็นเพิ่มเติม (ถ้ามี)'
                    : 'ระบุเหตุผลที่ปฏิเสธ',
              ),
              maxLines: 2,
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('ยกเลิก'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: isApprove ? AppTheme.success : AppTheme.error,
            ),
            child: const Text('ยืนยัน'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      final provider = Provider.of<LeaveProvider>(context, listen: false);
      final success = isApprove
          ? await provider.approveRequest(id, comment: commentController.text)
          : await provider.rejectRequest(id, comment: commentController.text);

      if (mounted) {
        if (success) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                isApprove ? 'อนุมัติเรียบร้อยแล้ว' : 'ปฏิเสธเรียบร้อยแล้ว',
              ),
              backgroundColor: isApprove ? AppTheme.success : AppTheme.error,
              behavior: SnackBarBehavior.floating,
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
      ),
      body: RefreshIndicator(
        onRefresh: () => leaveProvider.fetchPendingApprovals(),
        child: _buildContent(leaveProvider),
      ),
    );
  }

  Widget _buildContent(LeaveProvider provider) {
    if (provider.isLoading && provider.pendingApprovals.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    if (provider.pendingApprovals.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.fact_check_outlined, size: 100, color: Colors.grey[300]),
            const SizedBox(height: 20),
            Text(
              'ไม่มีรายการที่รอการอนุมัติ',
              style: TextStyle(
                color: Colors.grey[400],
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
      itemCount: provider.pendingApprovals.length,
      itemBuilder: (context, index) {
        final request = provider.pendingApprovals[index];
        return _buildApprovalCard(request);
      },
    );
  }

  Widget _buildApprovalCard(LeaveRequest request) {
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: AppTheme.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // User Info Header
          Padding(
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                CircleAvatar(
                  radius: 24,
                  backgroundColor: AppTheme.primaryLight,
                  backgroundImage: request.user?.avatarUrl != null
                      ? NetworkImage(request.user!.avatarUrl!)
                      : null,
                  child: request.user?.avatarUrl == null
                      ? const Icon(Icons.person, color: AppTheme.primary)
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
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                        ),
                      ),
                      Text(
                        request.user?.position ?? 'ไม่ระบุตำแหน่ง',
                        style: Theme.of(context).textTheme.bodyMedium,
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 10,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: AppTheme.primaryLight,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    request.leaveType.name,
                    style: const TextStyle(
                      color: AppTheme.primary,
                      fontWeight: FontWeight.bold,
                      fontSize: 12,
                    ),
                  ),
                ),
              ],
            ),
          ),

          const Divider(height: 1, color: AppTheme.border),

          // Request Details
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    _buildDetailItem(
                      Icons.calendar_today_rounded,
                      '${request.formattedStartDate} - ${request.formattedEndDate}',
                    ),
                    _buildDetailItem(
                      Icons.timer_outlined,
                      '${request.totalDays} วัน',
                    ),
                  ],
                ),
                if (request.reason.isNotEmpty) ...[
                  const SizedBox(height: 16),
                  Text(
                    'เหตุผล:',
                    style: Theme.of(context).textTheme.labelLarge,
                  ),
                  const SizedBox(height: 4),
                  Text(
                    request.reason,
                    style: Theme.of(context).textTheme.bodyMedium,
                  ),
                ],
              ],
            ),
          ),

          const Divider(height: 1, color: AppTheme.border),

          // Action Buttons
          Padding(
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: () => _handleApproval(request.id, false),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: AppTheme.error,
                      side: const BorderSide(color: AppTheme.error),
                      padding: const EdgeInsets.symmetric(vertical: 14),
                    ),
                    child: const Text('ปฏิเสธ'),
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: ElevatedButton(
                    onPressed: () => _handleApproval(request.id, true),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.success,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                    ),
                    child: const Text('อนุมัติ'),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDetailItem(IconData icon, String text) {
    return Row(
      children: [
        Icon(icon, size: 16, color: AppTheme.textSub),
        const SizedBox(width: 8),
        Text(text, style: const TextStyle(fontWeight: FontWeight.w600)),
      ],
    );
  }
}
