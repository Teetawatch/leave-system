import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../config/api_config.dart';
import '../../config/app_theme.dart';
import '../../models/guard_change_request.dart';
import '../../services/api_service.dart';
import '../../widgets/status_badge.dart';

class GuardChangeListScreen extends StatefulWidget {
  const GuardChangeListScreen({super.key});

  @override
  State<GuardChangeListScreen> createState() => _GuardChangeListScreenState();
}

class _GuardChangeListScreenState extends State<GuardChangeListScreen> with SingleTickerProviderStateMixin {
  final ApiService _api = ApiService();
  late TabController _tabController;
  List<GuardChangeRequest> _myRequests = [];
  List<GuardChangeRequest> _approvalRequests = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _loadData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    try {
      final results = await Future.wait([
        _api.get(ApiConfig.guardChangeRequests),
        _api.get(ApiConfig.guardChangeApprovals),
      ]);

      final myData = results[0]['data'];
      if (myData is List) {
        _myRequests = myData.map((r) => GuardChangeRequest.fromJson(r)).toList();
      }

      final approvalData = results[1]['data'];
      if (approvalData is List) {
        _approvalRequests = approvalData.map((r) => GuardChangeRequest.fromJson(r)).toList();
      }
    } catch (e) {
      debugPrint('Guard change load error: $e');
    }
    if (mounted) setState(() => _isLoading = false);
  }

  Future<void> _approveGuardChange(int id) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('ยืนยันตอบรับ'),
        content: const Text('คุณต้องการตอบรับการเปลี่ยนเวรนี้หรือไม่?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('ยกเลิก')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.success),
            child: const Text('ตอบรับ'),
          ),
        ],
      ),
    );
    if (confirmed != true) return;

    try {
      await _api.post(ApiConfig.guardChangeApprove(id), body: {'use_saved_signature': true});
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('ตอบรับเรียบร้อยแล้ว'), backgroundColor: AppTheme.success),
        );
        _loadData();
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('$e'), backgroundColor: AppTheme.error),
        );
      }
    }
  }

  Future<void> _rejectGuardChange(int id) async {
    final commentController = TextEditingController();
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('ปฏิเสธการเปลี่ยนเวร'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text('กรุณาระบุเหตุผล'),
            const SizedBox(height: 12),
            TextField(
              controller: commentController,
              maxLines: 3,
              decoration: InputDecoration(
                hintText: 'เหตุผล...',
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
      await _api.post(ApiConfig.guardChangeReject(id), body: {
        'comment': commentController.text.trim(),
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('ปฏิเสธเรียบร้อยแล้ว'), backgroundColor: AppTheme.success),
        );
        _loadData();
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
        title: Text('เปลี่ยนเวร', style: AppTheme.heading(18)),
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppTheme.primary,
          unselectedLabelColor: AppTheme.textMuted,
          indicatorColor: AppTheme.primary,
          labelStyle: GoogleFonts.prompt(fontWeight: FontWeight.w600, fontSize: 14),
          unselectedLabelStyle: GoogleFonts.prompt(fontWeight: FontWeight.w400, fontSize: 14),
          tabs: [
            Tab(text: 'คำขอของฉัน (${_myRequests.length})'),
            Tab(text: 'รอตอบรับ (${_approvalRequests.length})'),
          ],
        ),
      ),
      body: RefreshIndicator(
        onRefresh: _loadData,
        color: AppTheme.primary,
        child: _isLoading
            ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
            : TabBarView(
                controller: _tabController,
                children: [
                  _buildMyRequests(),
                  _buildApprovalRequests(),
                ],
              ),
      ),
      floatingActionButton: Container(
        decoration: BoxDecoration(
          gradient: AppTheme.primaryGradient,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: AppTheme.primary.withValues(alpha: 0.3),
              blurRadius: 12,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: FloatingActionButton.extended(
          onPressed: () async {
            final result = await Navigator.pushNamed(context, '/guard-change/create');
            if (result == true) _loadData();
          },
          icon: const Icon(Icons.swap_horiz_rounded),
          label: Text('ขอเปลี่ยนเวร', style: GoogleFonts.prompt(fontWeight: FontWeight.w600)),
          backgroundColor: Colors.transparent,
          foregroundColor: Colors.white,
          elevation: 0,
        ),
      ),
    );
  }

  Widget _buildMyRequests() {
    if (_myRequests.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: AppTheme.secondary.withValues(alpha: 0.06),
                shape: BoxShape.circle,
              ),
              child: Icon(Icons.swap_horiz_rounded, size: 48, color: AppTheme.textMuted.withValues(alpha: 0.5)),
            ),
            const SizedBox(height: 16),
            Text('ยังไม่มีคำขอเปลี่ยนเวร', style: AppTheme.heading(16, color: AppTheme.textMuted)),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _myRequests.length,
      itemBuilder: (_, i) => _buildRequestCard(_myRequests[i], showActions: false),
    );
  }

  Widget _buildApprovalRequests() {
    if (_approvalRequests.isEmpty) {
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
            Text('ไม่มีคำขอรอตอบรับ', style: AppTheme.heading(16, color: AppTheme.textMuted)),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _approvalRequests.length,
      itemBuilder: (_, i) => _buildRequestCard(_approvalRequests[i], showActions: true),
    );
  }

  Widget _buildRequestCard(GuardChangeRequest r, {required bool showActions}) {
    final dateFormat = DateFormat('d MMM yyyy', 'th');
    String dateText = '';
    try {
      dateText = dateFormat.format(DateTime.parse(r.dutyDate));
    } catch (_) {
      dateText = r.dutyDate;
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(18),
        boxShadow: AppTheme.softShadow,
      ),
      child: InkWell(
        onTap: () => Navigator.pushNamed(context, '/guard-change/detail', arguments: r.id),
        borderRadius: BorderRadius.circular(18),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: AppTheme.secondary.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.swap_horiz_rounded, color: AppTheme.secondary, size: 24),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(r.dutyPositionLabel, style: AppTheme.heading(15)),
                        const SizedBox(height: 2),
                        Text(dateText, style: AppTheme.body(13, color: AppTheme.textSecondary)),
                      ],
                    ),
                  ),
                  StatusBadge(status: r.status, label: r.statusLabel),
                ],
              ),
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: AppTheme.background,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: Column(
                        children: [
                          Text('ผู้ขอ', style: AppTheme.body(11, color: AppTheme.textMuted)),
                          const SizedBox(height: 2),
                          Text(
                            r.requesterName,
                            style: AppTheme.heading(13),
                            textAlign: TextAlign.center,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                    const Icon(Icons.arrow_forward_rounded, size: 18, color: AppTheme.textMuted),
                    Expanded(
                      child: Column(
                        children: [
                          Text('ผู้รับเปลี่ยน', style: AppTheme.body(11, color: AppTheme.textMuted)),
                          const SizedBox(height: 2),
                          Text(
                            r.replacementName,
                            style: AppTheme.heading(13),
                            textAlign: TextAlign.center,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              if (r.remarks != null && r.remarks!.isNotEmpty) ...[
                const SizedBox(height: 8),
                Text(r.remarks!, style: AppTheme.body(13, color: AppTheme.textSecondary), maxLines: 2, overflow: TextOverflow.ellipsis),
              ],
              if (showActions && r.isPending) ...[
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton.icon(
                        onPressed: () => _rejectGuardChange(r.id),
                        icon: const Icon(Icons.close_rounded, size: 18),
                        label: Text('ปฏิเสธ', style: GoogleFonts.prompt(fontWeight: FontWeight.w600)),
                        style: OutlinedButton.styleFrom(
                          foregroundColor: AppTheme.error,
                          side: const BorderSide(color: AppTheme.error),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                          padding: const EdgeInsets.symmetric(vertical: 12),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ElevatedButton.icon(
                        onPressed: () => _approveGuardChange(r.id),
                        icon: const Icon(Icons.check_rounded, size: 18),
                        label: Text('ตอบรับ', style: GoogleFonts.prompt(fontWeight: FontWeight.w600)),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppTheme.success,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                          padding: const EdgeInsets.symmetric(vertical: 12),
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
