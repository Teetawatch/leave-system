import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../config/api_config.dart';
import '../../config/app_theme.dart';
import '../../models/guard_change_request.dart';
import '../../services/api_service.dart';
import '../../widgets/status_badge.dart';

class GuardChangeDetailScreen extends StatefulWidget {
  final int requestId;
  const GuardChangeDetailScreen({super.key, required this.requestId});

  @override
  State<GuardChangeDetailScreen> createState() => _GuardChangeDetailScreenState();
}

class _GuardChangeDetailScreenState extends State<GuardChangeDetailScreen> {
  final ApiService _api = ApiService();
  GuardChangeRequest? _request;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadDetail();
  }

  Future<void> _loadDetail() async {
    setState(() => _isLoading = true);
    try {
      final response = await _api.get(ApiConfig.guardChangeDetail(widget.requestId));
      if (response['success'] == true) {
        _request = GuardChangeRequest.fromJson(response['data']);
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('$e'), backgroundColor: AppTheme.error),
        );
      }
    }
    if (mounted) setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('รายละเอียดเปลี่ยนเวร', style: AppTheme.heading(18))),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
          : _request == null
              ? const Center(child: Text('ไม่พบข้อมูล'))
              : RefreshIndicator(
                  onRefresh: _loadDetail,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildHeader(),
                        const SizedBox(height: 20),
                        _buildSwapInfo(),
                        const SizedBox(height: 20),
                        _buildDetailInfo(),
                        const SizedBox(height: 20),
                        _buildApprovalStatus(),
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildHeader() {
    final r = _request!;
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            AppTheme.getStatusColor(r.status).withValues(alpha: 0.08),
            AppTheme.getStatusColor(r.status).withValues(alpha: 0.02),
          ],
        ),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.getStatusColor(r.status).withValues(alpha: 0.2)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: AppTheme.secondary.withValues(alpha: 0.15),
              borderRadius: BorderRadius.circular(14),
            ),
            child: const Icon(Icons.swap_horiz_rounded, color: AppTheme.secondary, size: 28),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(r.dutyPositionLabel, style: AppTheme.heading(20)),
                const SizedBox(height: 4),
                Text('คำขอ #${r.id}', style: AppTheme.body(13, color: AppTheme.textSecondary)),
              ],
            ),
          ),
          StatusBadge(status: r.status, label: r.statusLabel),
        ],
      ),
    );
  }

  Widget _buildSwapInfo() {
    final r = _request!;
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(18),
        boxShadow: AppTheme.softShadow,
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              children: [
                CircleAvatar(
                  radius: 28,
                  backgroundColor: AppTheme.primary.withValues(alpha: 0.1),
                  child: Text(
                    (r.user?['name'] ?? 'U')[0].toUpperCase(),
                    style: GoogleFonts.prompt(color: AppTheme.primary, fontWeight: FontWeight.w600, fontSize: 20),
                  ),
                ),
                const SizedBox(height: 8),
                Text('ผู้ขอเปลี่ยน', style: AppTheme.body(11, color: AppTheme.textMuted)),
                const SizedBox(height: 2),
                Text(
                  r.requesterName,
                  style: AppTheme.heading(13),
                  textAlign: TextAlign.center,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: AppTheme.secondary.withValues(alpha: 0.1),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.swap_horiz_rounded, color: AppTheme.secondary, size: 24),
          ),
          Expanded(
            child: Column(
              children: [
                CircleAvatar(
                  radius: 28,
                  backgroundColor: AppTheme.success.withValues(alpha: 0.1),
                  child: Text(
                    (r.replacementUser?['name'] ?? 'U')[0].toUpperCase(),
                    style: GoogleFonts.prompt(color: AppTheme.success, fontWeight: FontWeight.w600, fontSize: 20),
                  ),
                ),
                const SizedBox(height: 8),
                Text('ผู้รับเปลี่ยน', style: AppTheme.body(11, color: AppTheme.textMuted)),
                const SizedBox(height: 2),
                Text(
                  r.replacementName,
                  style: AppTheme.heading(13),
                  textAlign: TextAlign.center,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDetailInfo() {
    final r = _request!;
    final dateFormat = DateFormat('d MMMM yyyy', 'th');
    String dateText = r.dutyDate;
    try {
      dateText = dateFormat.format(DateTime.parse(r.dutyDate));
    } catch (_) {}

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(18),
        boxShadow: AppTheme.softShadow,
      ),
      child: Column(
        children: [
          _infoRow(Icons.calendar_today_rounded, 'วันที่เวร', dateText),
          const Divider(height: 24),
          _infoRow(Icons.work_outline_rounded, 'ตำแหน่งเวร', r.dutyPositionLabel),
          if (r.remarks != null && r.remarks!.isNotEmpty) ...[
            const Divider(height: 24),
            _infoRow(Icons.chat_bubble_outline_rounded, 'หมายเหตุ', r.remarks!),
          ],
        ],
      ),
    );
  }

  Widget _infoRow(IconData icon, String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 20, color: AppTheme.primary),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: AppTheme.body(12, color: AppTheme.textMuted)),
              const SizedBox(height: 2),
              Text(value, style: AppTheme.heading(15)),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildApprovalStatus() {
    final r = _request!;

    final steps = <Map<String, dynamic>>[];

    // Step 1: Replacement user approval
    steps.add({
      'label': 'ผู้รับเปลี่ยนเวร',
      'name': r.replacementName,
      'done': r.approvedAt != null,
      'rejected': r.isRejected && r.directorApprovedAt == null && r.finalApprovedAt == null,
      'comment': r.approvalComment,
    });

    // Step 2: Deputy Director
    if (r.approvedAt != null && !r.isRejected) {
      steps.add({
        'label': 'รอง ผอ.',
        'name': r.directorApprover != null ? '${r.directorApprover!['rank'] ?? ''} ${r.directorApprover!['name'] ?? ''}' : '',
        'done': r.directorApprovedAt != null,
        'rejected': false,
        'comment': r.directorComment,
      });
    }

    // Step 3: Director
    if (r.directorApprovedAt != null) {
      steps.add({
        'label': 'ผอ.',
        'name': r.finalApprover != null ? '${r.finalApprover!['rank'] ?? ''} ${r.finalApprover!['name'] ?? ''}' : '',
        'done': r.finalApprovedAt != null,
        'rejected': false,
        'comment': r.finalComment,
      });
    }

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(18),
        boxShadow: AppTheme.softShadow,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: AppTheme.primary.withValues(alpha: 0.08),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Icon(Icons.timeline_rounded, size: 18, color: AppTheme.primary),
              ),
              const SizedBox(width: 10),
              Text('ขั้นตอนการอนุมัติ', style: AppTheme.heading(16)),
            ],
          ),
          const SizedBox(height: 16),
          ...steps.asMap().entries.map((entry) {
            final i = entry.key;
            final step = entry.value;
            final isLast = i == steps.length - 1;
            final isDone = step['done'] as bool;
            final isRejected = step['rejected'] as bool;
            final color = isRejected ? AppTheme.error : (isDone ? AppTheme.success : AppTheme.textMuted);

            return IntrinsicHeight(
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Column(
                    children: [
                      Container(
                        width: 32,
                        height: 32,
                        decoration: BoxDecoration(
                          color: color.withValues(alpha: 0.15),
                          shape: BoxShape.circle,
                        ),
                        child: Icon(
                          isRejected ? Icons.close_rounded : (isDone ? Icons.check_rounded : Icons.schedule_rounded),
                          color: color,
                          size: 18,
                        ),
                      ),
                      if (!isLast)
                        Expanded(child: Container(width: 2, color: AppTheme.divider)),
                    ],
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Padding(
                      padding: EdgeInsets.only(bottom: isLast ? 0 : 16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(step['label'], style: AppTheme.body(12, color: AppTheme.textMuted)),
                          if ((step['name'] as String).isNotEmpty)
                            Text(step['name'], style: AppTheme.heading(14)),
                          Text(
                            isRejected ? 'ปฏิเสธ' : (isDone ? 'อนุมัติแล้ว' : 'รอดำเนินการ'),
                            style: AppTheme.body(12, color: color),
                          ),
                          if (step['comment'] != null && (step['comment'] as String).isNotEmpty)
                            Padding(
                              padding: const EdgeInsets.only(top: 4),
                              child: Text(step['comment'], style: AppTheme.body(13, color: AppTheme.textSecondary)),
                            ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            );
          }),
        ],
      ),
    );
  }
}
