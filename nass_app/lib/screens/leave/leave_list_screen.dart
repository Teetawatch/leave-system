import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../config/api_config.dart';
import '../../config/app_theme.dart';
import '../../models/leave_request.dart';
import '../../services/api_service.dart';
import '../../widgets/status_badge.dart';

class LeaveListScreen extends StatefulWidget {
  const LeaveListScreen({super.key});

  @override
  State<LeaveListScreen> createState() => _LeaveListScreenState();
}

class _LeaveListScreenState extends State<LeaveListScreen> {
  final ApiService _api = ApiService();
  List<LeaveRequest> _requests = [];
  bool _isLoading = true;
  String _selectedStatus = 'all';

  final _statusOptions = [
    {'value': 'all', 'label': 'ทั้งหมด'},
    {'value': 'pending_supervisor', 'label': 'รออนุมัติ'},
    {'value': 'approved', 'label': 'อนุมัติแล้ว'},
    {'value': 'rejected', 'label': 'ไม่อนุมัติ'},
    {'value': 'cancelled', 'label': 'ยกเลิก'},
  ];

  @override
  void initState() {
    super.initState();
    _loadRequests();
  }

  Future<void> _loadRequests() async {
    setState(() => _isLoading = true);
    try {
      final queryParams = <String, String>{};
      if (_selectedStatus != 'all') {
        queryParams['status'] = _selectedStatus;
      }
      final response = await _api.get(ApiConfig.leaveRequests, queryParams: queryParams);
      final data = response['data'];
      final list = data is List ? data : [];
      _requests = list.map((r) => LeaveRequest.fromJson(r)).toList();
    } catch (e) {
      debugPrint('Leave list error: $e');
    }
    if (mounted) setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('การลาของฉัน', style: AppTheme.heading(18)),
      ),
      body: Column(
        children: [
          // Status Filter
          Container(
            height: 52,
            margin: const EdgeInsets.only(top: 4),
            child: ListView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              children: _statusOptions.map((opt) {
                final isSelected = _selectedStatus == opt['value'];
                return Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: GestureDetector(
                    onTap: () {
                      setState(() => _selectedStatus = opt['value']!);
                      _loadRequests();
                    },
                    child: AnimatedContainer(
                      duration: const Duration(milliseconds: 200),
                      padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 10),
                      decoration: BoxDecoration(
                        color: isSelected ? AppTheme.primary : AppTheme.surface,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(
                          color: isSelected ? AppTheme.primary : AppTheme.divider.withValues(alpha: 0.5),
                        ),
                        boxShadow: isSelected ? AppTheme.softShadow : null,
                      ),
                      child: Center(
                        child: Text(
                          opt['label']!,
                          style: GoogleFonts.prompt(
                            color: isSelected ? Colors.white : AppTheme.textSecondary,
                            fontWeight: isSelected ? FontWeight.w600 : FontWeight.w400,
                            fontSize: 13,
                          ),
                        ),
                      ),
                    ),
                  ),
                );
              }).toList(),
            ),
          ),
          const SizedBox(height: 8),

          // List
          Expanded(
            child: RefreshIndicator(
              onRefresh: _loadRequests,
              color: AppTheme.primary,
              child: _isLoading
                  ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
                  : _requests.isEmpty
                      ? _buildEmpty()
                      : ListView.builder(
                          padding: const EdgeInsets.symmetric(horizontal: 16),
                          itemCount: _requests.length,
                          itemBuilder: (_, i) => _buildRequestCard(_requests[i]),
                        ),
            ),
          ),
        ],
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
            final result = await Navigator.pushNamed(context, '/leave/create');
            if (result == true) _loadRequests();
          },
          icon: const Icon(Icons.add_rounded),
          label: Text('ยื่นลา', style: GoogleFonts.prompt(fontWeight: FontWeight.w600)),
          backgroundColor: Colors.transparent,
          foregroundColor: Colors.white,
          elevation: 0,
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
              color: AppTheme.primary.withValues(alpha: 0.06),
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.event_note_rounded, size: 48, color: AppTheme.textMuted.withValues(alpha: 0.5)),
          ),
          const SizedBox(height: 20),
          Text('ยังไม่มีประวัติการลา', style: AppTheme.heading(16, color: AppTheme.textMuted)),
          const SizedBox(height: 8),
          Text('เริ่มต้นยื่นลาได้เลย', style: AppTheme.body(14, color: AppTheme.textMuted)),
          const SizedBox(height: 20),
          Container(
            decoration: BoxDecoration(
              gradient: AppTheme.primaryGradient,
              borderRadius: BorderRadius.circular(14),
            ),
            child: ElevatedButton.icon(
              onPressed: () => Navigator.pushNamed(context, '/leave/create'),
              icon: const Icon(Icons.add_rounded, size: 20),
              label: Text('ยื่นลาเลย', style: GoogleFonts.prompt(fontWeight: FontWeight.w600)),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.transparent,
                shadowColor: Colors.transparent,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRequestCard(LeaveRequest r) {
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
      margin: const EdgeInsets.only(bottom: 10),
      child: Material(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(18),
        child: InkWell(
          onTap: () async {
            final result = await Navigator.pushNamed(context, '/leave/detail', arguments: r.id);
            if (result == true) _loadRequests();
          },
          borderRadius: BorderRadius.circular(18),
          child: Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(18),
              boxShadow: AppTheme.softShadow,
            ),
            child: Row(
              children: [
                Container(
                  width: 50,
                  height: 50,
                  decoration: BoxDecoration(
                    color: AppTheme.getStatusBgColor(r.status),
                    borderRadius: BorderRadius.circular(15),
                  ),
                  child: Icon(
                    AppTheme.getStatusIcon(r.status),
                    color: AppTheme.getStatusColor(r.status),
                    size: 24,
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: Text(
                              r.leaveType?.name ?? 'ลา',
                              style: AppTheme.heading(15),
                            ),
                          ),
                          StatusBadge(status: r.status, label: r.statusLabel),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Text(dateText, style: AppTheme.body(13, color: AppTheme.textSecondary)),
                      const SizedBox(height: 2),
                      Text(
                        '${r.totalDays.toStringAsFixed(0)} วัน',
                        style: AppTheme.body(12, color: AppTheme.textMuted),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 4),
                Container(
                  padding: const EdgeInsets.all(6),
                  decoration: BoxDecoration(
                    color: AppTheme.background,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Icon(Icons.chevron_right_rounded, color: AppTheme.textMuted, size: 18),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
