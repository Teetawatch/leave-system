import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../config/api_config.dart';
import '../../config/app_theme.dart';
import '../../models/leave_request.dart';
import '../../services/api_service.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/user_avatar.dart';

class ApprovalListScreen extends StatefulWidget {
  const ApprovalListScreen({super.key});

  @override
  State<ApprovalListScreen> createState() => _ApprovalListScreenState();
}

class _ApprovalListScreenState extends State<ApprovalListScreen> {
  final ApiService _api = ApiService();
  List<LeaveRequest> _requests = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadApprovals();
  }

  Future<void> _loadApprovals() async {
    setState(() => _isLoading = true);
    try {
      final response = await _api.get(ApiConfig.approvals);
      final data = response['data'];
      final list = data is List ? data : [];
      _requests = list.map((r) => LeaveRequest.fromJson(r)).toList();
    } catch (e) {
      debugPrint('Load approvals error: $e');
    }
    if (mounted) setState(() => _isLoading = false);
  }

  Future<void> _approve(int id) async {
    final confirmed = await _showConfirmDialog('อนุมัติ', 'คุณต้องการอนุมัติใบลานี้หรือไม่?');
    if (confirmed != true) return;

    try {
      await _api.post(ApiConfig.approvalApprove(id), body: {'use_saved_signature': true});
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('อนุมัติเรียบร้อยแล้ว'), backgroundColor: AppTheme.success),
        );
        _loadApprovals();
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('$e'), backgroundColor: AppTheme.error),
        );
      }
    }
  }

  Future<void> _reject(int id) async {
    final commentController = TextEditingController();
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('ปฏิเสธใบลา'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text('กรุณาระบุเหตุผล (ถ้ามี)'),
            const SizedBox(height: 12),
            TextField(
              controller: commentController,
              maxLines: 3,
              decoration: InputDecoration(
                hintText: 'เหตุผลที่ปฏิเสธ...',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('ยกเลิก')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.error),
            child: const Text('ปฏิเสธ'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    try {
      await _api.post(ApiConfig.approvalReject(id), body: {
        'comment': commentController.text.trim(),
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('ปฏิเสธเรียบร้อยแล้ว'), backgroundColor: AppTheme.success),
        );
        _loadApprovals();
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('$e'), backgroundColor: AppTheme.error),
        );
      }
    }
  }

  Future<bool?> _showConfirmDialog(String title, String content) {
    return showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Text(title),
        content: Text(content),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('ยกเลิก')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('ยืนยัน'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('รออนุมัติ', style: AppTheme.heading(18))),
      body: RefreshIndicator(
        onRefresh: _loadApprovals,
        color: AppTheme.primary,
        child: _isLoading
            ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
            : _requests.isEmpty
                ? _buildEmpty()
                : ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _requests.length,
                    itemBuilder: (_, i) => _buildApprovalCard(_requests[i]),
                  ),
      ),
    );
  }

  Widget _buildEmpty() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: AppTheme.success.withValues(alpha: 0.06),
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.check_circle_outline_rounded, size: 48, color: AppTheme.success.withValues(alpha: 0.5)),
          ),
          const SizedBox(height: 16),
          Text('ไม่มีใบลารออนุมัติ', style: AppTheme.heading(16, color: AppTheme.textMuted)),
        ],
      ),
    );
  }

  Widget _buildApprovalCard(LeaveRequest r) {
    final dateFormat = DateFormat('d MMM yyyy', 'th');
    String dateText = '';
    try {
      final start = DateTime.parse(r.startDate);
      final end = DateTime.parse(r.endDate);
      dateText = '${dateFormat.format(start)} - ${dateFormat.format(end)}';
    } catch (_) {
      dateText = '${r.startDate} - ${r.endDate}';
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(18),
        boxShadow: AppTheme.softShadow,
      ),
      child: Column(
        children: [
          // Header
          Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              children: [
                UserAvatar(
                  name: r.user?.name ?? 'U',
                  imageUrl: r.user?.avatarUrl,
                  radius: 22,
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        r.user?.displayName ?? 'ไม่ทราบชื่อ',
                        style: AppTheme.heading(15),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        r.user?.department ?? '',
                        style: AppTheme.body(12, color: AppTheme.textMuted),
                      ),
                    ],
                  ),
                ),
                StatusBadge(status: r.status, label: r.statusLabel),
              ],
            ),
          ),

          // Info
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: AppTheme.background,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Column(
                children: [
                  Row(
                    children: [
                      const Icon(Icons.event_note_rounded, size: 16, color: AppTheme.primary),
                      const SizedBox(width: 6),
                      Text(r.leaveType?.name ?? 'ลา', style: AppTheme.heading(14)),
                      const Spacer(),
                      Text('${r.totalDays.toStringAsFixed(0)} วัน', style: AppTheme.heading(14, color: AppTheme.primary)),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Row(
                    children: [
                      const Icon(Icons.date_range_rounded, size: 16, color: AppTheme.textMuted),
                      const SizedBox(width: 6),
                      Text(dateText, style: AppTheme.body(13, color: AppTheme.textSecondary)),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Icon(Icons.chat_bubble_outline_rounded, size: 16, color: AppTheme.textMuted),
                      const SizedBox(width: 6),
                      Expanded(
                        child: Text(r.reason, style: AppTheme.body(13, color: AppTheme.textSecondary), maxLines: 2, overflow: TextOverflow.ellipsis),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),

          // Action buttons
          Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () => _reject(r.id),
                    icon: const Icon(Icons.close_rounded, size: 18),
                    label: Text('ปฏิเสธ', style: GoogleFonts.prompt(fontWeight: FontWeight.w600)),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: AppTheme.error,
                      side: const BorderSide(color: AppTheme.error),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                      padding: const EdgeInsets.symmetric(vertical: 14),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () => _approve(r.id),
                    icon: const Icon(Icons.check_rounded, size: 18),
                    label: Text('อนุมัติ', style: GoogleFonts.prompt(fontWeight: FontWeight.w600)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.success,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                      padding: const EdgeInsets.symmetric(vertical: 14),
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
}
