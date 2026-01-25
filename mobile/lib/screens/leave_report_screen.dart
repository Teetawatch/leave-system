import 'package:flutter/material.dart';
import '../config/app_theme.dart';
import '../services/report_service.dart';
import 'package:intl/intl.dart';

class LeaveReportScreen extends StatefulWidget {
  const LeaveReportScreen({super.key});

  @override
  State<LeaveReportScreen> createState() => _LeaveReportScreenState();
}

class _LeaveReportScreenState extends State<LeaveReportScreen> {
  final ReportService _reportService = ReportService();
  bool _isLoading = true;
  Map<String, dynamic>? _data;
  String? _selectedDepartment;
  String? _selectedStatus;
  DateTimeRange? _selectedDateRange;

  @override
  void initState() {
    super.initState();
    _fetchData();
  }

  Future<void> _fetchData() async {
    setState(() => _isLoading = true);
    try {
      final data = await _reportService.getLeaveSummary(
        startDate: _selectedDateRange?.start != null
            ? DateFormat('yyyy-MM-dd').format(_selectedDateRange!.start)
            : null,
        endDate: _selectedDateRange?.end != null
            ? DateFormat('yyyy-MM-dd').format(_selectedDateRange!.end)
            : null,
        department: _selectedDepartment,
        status: _selectedStatus,
      );
      setState(() {
        _data = data;
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('เกิดข้อผิดพลาด: ${e.toString()}')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('รายงานสรุปการลา'),
        backgroundColor: Colors.white,
        elevation: 0,
        foregroundColor: AppTheme.textMain,
      ),
      body: Column(
        children: [
          _buildFilters(),
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : _buildReportContent(),
          ),
        ],
      ),
    );
  }

  Widget _buildFilters() {
    return Container(
      padding: const EdgeInsets.all(16),
      color: Colors.white,
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: () async {
                    final range = await showDateRangePicker(
                      context: context,
                      firstDate: DateTime(2024),
                      lastDate: DateTime(2026),
                      initialDateRange: _selectedDateRange,
                    );
                    if (range != null) {
                      setState(() => _selectedDateRange = range);
                      _fetchData();
                    }
                  },
                  icon: const Icon(Icons.date_range_rounded, size: 18),
                  label: Text(
                    _selectedDateRange == null
                        ? 'เลือกช่วงวันที่'
                        : '${DateFormat('dd/MM/yy').format(_selectedDateRange!.start)} - ${DateFormat('dd/MM/yy').format(_selectedDateRange!.end)}',
                    style: const TextStyle(fontSize: 12),
                  ),
                ),
              ),
              const SizedBox(width: 8),
              IconButton(
                onPressed: () {
                  setState(() {
                    _selectedDateRange = null;
                    _selectedDepartment = null;
                    _selectedStatus = null;
                  });
                  _fetchData();
                },
                icon: const Icon(Icons.refresh_rounded),
                color: AppTheme.primary,
              ),
            ],
          ),
          const SizedBox(height: 8),
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: [
                _buildFilterChip('ทั้งหมด', null, _selectedStatus == null, (
                  val,
                ) {
                  setState(() => _selectedStatus = null);
                  _fetchData();
                }),
                _buildFilterChip(
                  'อนุมัติแล้ว',
                  'approved',
                  _selectedStatus == 'approved',
                  (val) {
                    setState(() => _selectedStatus = 'approved');
                    _fetchData();
                  },
                ),
                _buildFilterChip(
                  'รออนุมัติ',
                  'pending',
                  _selectedStatus == 'pending',
                  (val) {
                    setState(() => _selectedStatus = 'pending');
                    _fetchData();
                  },
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterChip(
    String label,
    String? value,
    bool isSelected,
    Function(String?) onSelected,
  ) {
    return Padding(
      padding: const EdgeInsets.only(right: 8.0),
      child: FilterChip(
        label: Text(label),
        selected: isSelected,
        onSelected: (selected) => onSelected(value),
        selectedColor: AppTheme.primary.withOpacity(0.2),
        checkmarkColor: AppTheme.primary,
        labelStyle: TextStyle(
          color: isSelected ? AppTheme.primary : AppTheme.textSub,
          fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
          fontSize: 12,
        ),
      ),
    );
  }

  Widget _buildReportContent() {
    if (_data == null || (_data!['requests'] as List).isEmpty) {
      return const Center(child: Text('ไม่พบข้อมูลในช่วงเวลาที่เลือก'));
    }

    final requests = _data!['requests'] as List;
    final stats = _data!['stats'] as Map<String, dynamic>;

    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        _buildStatsCards(stats),
        const SizedBox(height: 24),
        const Text(
          'รายการคำขอ',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
        ),
        const SizedBox(height: 16),
        ...requests.map((req) => _buildRequestItem(req)).toList(),
      ],
    );
  }

  Widget _buildStatsCards(Map<String, dynamic> stats) {
    return Row(
      children: [
        Expanded(
          child: _buildStatCard(
            'อนุมัติแล้ว',
            stats['total_approved_leaves'].toString(),
            AppTheme.success,
            Icons.check_circle_rounded,
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: _buildStatCard(
            'รออนุมัติ',
            stats['total_pending'].toString(),
            AppTheme.warning,
            Icons.pending_rounded,
          ),
        ),
      ],
    );
  }

  Widget _buildStatCard(
    String label,
    String value,
    Color color,
    IconData icon,
  ) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: color.withOpacity(0.2)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 24),
          const SizedBox(height: 12),
          Text(
            value,
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          Text(
            label,
            style: TextStyle(fontSize: 12, color: color.withOpacity(0.8)),
          ),
        ],
      ),
    );
  }

  Widget _buildRequestItem(dynamic req) {
    final status = req['status'] as String;
    final color = _getStatusColor(status);

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          CircleAvatar(
            backgroundColor: color.withOpacity(0.1),
            child: Text(
              (req['user']['name'] as String).substring(0, 1),
              style: TextStyle(color: color, fontWeight: FontWeight.bold),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  req['user']['name'],
                  style: const TextStyle(fontWeight: FontWeight.bold),
                ),
                Text(
                  '${req['leave_type']['name']} (${req['total_days']} วัน)',
                  style: TextStyle(color: AppTheme.textSub, fontSize: 12),
                ),
                Text(
                  '${req['start_date']} - ${req['end_date']}',
                  style: TextStyle(color: AppTheme.textSub, fontSize: 11),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Text(
              _getStatusText(status),
              style: TextStyle(
                color: color,
                fontSize: 10,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Color _getStatusColor(String status) {
    if (status == 'approved') return AppTheme.success;
    if (status == 'rejected') return AppTheme.error;
    if (status.startsWith('pending')) return AppTheme.warning;
    return Colors.grey;
  }

  String _getStatusText(String status) {
    if (status == 'approved') return 'อนุมัติแล้ว';
    if (status == 'rejected') return 'ปฏิเสธ';
    if (status.startsWith('pending')) return 'รออนุมัติ';
    return status;
  }
}
