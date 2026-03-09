import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../config/api_config.dart';
import '../../config/app_theme.dart';
import '../../models/leave_request.dart';
import '../../services/api_service.dart';
import '../../widgets/status_badge.dart';

class LeaveDetailScreen extends StatefulWidget {
  final int requestId;
  const LeaveDetailScreen({super.key, required this.requestId});

  @override
  State<LeaveDetailScreen> createState() => _LeaveDetailScreenState();
}

class _LeaveDetailScreenState extends State<LeaveDetailScreen> {
  final ApiService _api = ApiService();
  LeaveRequest? _request;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadDetail();
  }

  Future<void> _loadDetail() async {
    setState(() => _isLoading = true);
    try {
      final response = await _api.get(ApiConfig.leaveRequestDetail(widget.requestId));
      if (response['success'] == true) {
        _request = LeaveRequest.fromJson(response['data']);
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

  Future<void> _cancelRequest() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('ยืนยันยกเลิก'),
        content: const Text('คุณต้องการยกเลิกใบลานี้หรือไม่?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('ไม่')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.error),
            child: const Text('ยกเลิกใบลา'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    try {
      await _api.post(ApiConfig.leaveRequestCancel(widget.requestId));
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('ยกเลิกใบลาเรียบร้อยแล้ว'), backgroundColor: AppTheme.success),
        );
        Navigator.pop(context, true);
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('$e'), backgroundColor: AppTheme.error),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('รายละเอียดใบลา', style: AppTheme.heading(18)),
        actions: [
          if (_request != null && _request!.canCancel)
            IconButton(
              onPressed: _cancelRequest,
              icon: const Icon(Icons.cancel_outlined, color: AppTheme.error),
              tooltip: 'ยกเลิกใบลา',
            ),
        ],
      ),
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
                        _buildInfoSection(),
                        const SizedBox(height: 20),
                        _buildApprovalTimeline(),
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
              color: AppTheme.getStatusBgColor(r.status),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Icon(
              _getLeaveIcon(r.leaveType?.slug),
              color: AppTheme.getStatusColor(r.status),
              size: 28,
            ),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(r.leaveType?.name ?? 'ลา', style: AppTheme.heading(20)),
                const SizedBox(height: 4),
                Text('ใบลา #${r.id}', style: AppTheme.body(13, color: AppTheme.textSecondary)),
              ],
            ),
          ),
          StatusBadge(status: r.status, label: r.statusLabel),
        ],
      ),
    );
  }

  Widget _buildInfoSection() {
    final r = _request!;
    final dateFormat = DateFormat('d MMMM yyyy', 'th');
    String startText = r.startDate;
    String endText = r.endDate;
    try {
      startText = dateFormat.format(DateTime.parse(r.startDate));
      endText = dateFormat.format(DateTime.parse(r.endDate));
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
          _infoRow(Icons.calendar_today_rounded, 'วันเริ่มต้น', startText),
          const Divider(height: 24),
          _infoRow(Icons.event_rounded, 'วันสิ้นสุด', endText),
          const Divider(height: 24),
          _infoRow(Icons.timelapse_rounded, 'จำนวนวัน', '${r.totalDays.toStringAsFixed(0)} วัน'),
          const Divider(height: 24),
          _infoRow(Icons.chat_bubble_outline_rounded, 'เหตุผล', r.reason),
          if (r.user != null) ...[
            const Divider(height: 24),
            _infoRow(Icons.person_outline_rounded, 'ผู้ขอลา', r.user!.displayName),
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
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label, style: AppTheme.body(12, color: AppTheme.textMuted)),
            const SizedBox(height: 2),
            SizedBox(
              width: MediaQuery.of(context).size.width - 120,
              child: Text(value, style: AppTheme.heading(15)),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildApprovalTimeline() {
    final r = _request!;
    if (r.approvals.isEmpty) {
      return Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: AppTheme.surface,
          borderRadius: BorderRadius.circular(18),
          boxShadow: AppTheme.softShadow,
        ),
        child: Center(
          child: Text('ยังไม่มีการดำเนินการ', style: AppTheme.body(14, color: AppTheme.textMuted)),
        ),
      );
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
          ...r.approvals.asMap().entries.map((entry) {
            final i = entry.key;
            final a = entry.value;
            final isLast = i == r.approvals.length - 1;
            final color = a.isApproved || a.isAcknowledged ? AppTheme.success : AppTheme.error;

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
                          a.isApproved || a.isAcknowledged ? Icons.check_rounded : Icons.close_rounded,
                          color: color,
                          size: 18,
                        ),
                      ),
                      if (!isLast)
                        Expanded(
                          child: Container(
                            width: 2,
                            color: AppTheme.divider,
                          ),
                        ),
                    ],
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Padding(
                      padding: EdgeInsets.only(bottom: isLast ? 0 : 16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            a.approver?.displayName ?? 'ผู้อนุมัติ',
                            style: AppTheme.heading(14),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            '${_getStepLabel(a.step)} - ${_getActionLabel(a.action)}',
                            style: AppTheme.body(12, color: color),
                          ),
                          if (a.comment != null && a.comment!.isNotEmpty) ...[
                            const SizedBox(height: 4),
                            Text(
                              a.comment!,
                              style: AppTheme.body(13, color: AppTheme.textSecondary),
                            ),
                          ],
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

  String _getStepLabel(String step) {
    switch (step) {
      case 'supervisor':
        return 'หัวหน้างาน';
      case 'manager':
        return 'ผู้บังคับบัญชา';
      case 'deputy_director':
        return 'รอง ผอ.';
      case 'director':
        return 'ผอ.';
      default:
        return step;
    }
  }

  String _getActionLabel(String action) {
    switch (action) {
      case 'approved':
        return 'อนุมัติ';
      case 'rejected':
        return 'ปฏิเสธ';
      case 'acknowledged':
        return 'รับทราบ';
      default:
        return action;
    }
  }

  IconData _getLeaveIcon(String? slug) {
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
