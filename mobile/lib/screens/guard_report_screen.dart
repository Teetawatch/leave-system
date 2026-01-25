import 'package:flutter/material.dart';
import '../config/app_theme.dart';
import '../services/report_service.dart';
import 'package:intl/intl.dart';

class GuardReportScreen extends StatefulWidget {
  const GuardReportScreen({super.key});

  @override
  State<GuardReportScreen> createState() => _GuardReportScreenState();
}

class _GuardReportScreenState extends State<GuardReportScreen> {
  final ReportService _reportService = ReportService();
  bool _isLoading = true;
  Map<String, dynamic>? _data;
  DateTimeRange? _selectedDateRange;
  String? _selectedStatus;

  @override
  void initState() {
    super.initState();
    _fetchData();
  }

  Future<void> _fetchData() async {
    setState(() => _isLoading = true);
    try {
      final data = await _reportService.getGuardChangeSummary(
        startDate: _selectedDateRange?.start != null
            ? DateFormat('yyyy-MM-dd').format(_selectedDateRange!.start)
            : null,
        endDate: _selectedDateRange?.end != null
            ? DateFormat('yyyy-MM-dd').format(_selectedDateRange!.end)
            : null,
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
        title: const Text('รายงานการเปลี่ยนเวรยาม'),
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
                  'fully_approved',
                  _selectedStatus == 'fully_approved',
                  (val) {
                    setState(() => _selectedStatus = 'fully_approved');
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
          'รายการคำขอเปลี่ยนเวร',
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
            'ทั้งหมด',
            stats['total'].toString(),
            AppTheme.primary,
            Icons.list_alt_rounded,
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: _buildStatCard(
            'อนุมัติแล้ว',
            stats['approved'].toString(),
            AppTheme.success,
            Icons.check_circle_rounded,
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: _buildStatCard(
            'รออนุมัติ',
            stats['pending'].toString(),
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
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: color.withOpacity(0.2)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 20),
          const SizedBox(height: 8),
          Text(
            value,
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          Text(
            label,
            style: TextStyle(fontSize: 10, color: color.withOpacity(0.8)),
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
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'ผู้ขอ: ${req['user']['name']}',
                      style: const TextStyle(fontWeight: FontWeight.bold),
                    ),
                    Text(
                      'ผู้แทน: ${req['replacement_user']['name']}',
                      style: TextStyle(color: AppTheme.textSub, fontSize: 13),
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
          const Divider(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'วันที่ปฏิบัติเวร',
                    style: TextStyle(fontSize: 11, color: Colors.grey),
                  ),
                  Text(
                    req['duty_date'] ?? '-',
                    style: const TextStyle(fontWeight: FontWeight.w500),
                  ),
                ],
              ),
              if (req['approver'] != null)
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    const Text(
                      'ผู้อนุมัติ',
                      style: TextStyle(fontSize: 11, color: Colors.grey),
                    ),
                    Text(
                      req['approver']['name'],
                      style: const TextStyle(fontWeight: FontWeight.w500),
                    ),
                  ],
                ),
            ],
          ),
        ],
      ),
    );
  }

  Color _getStatusColor(String status) {
    if (status == 'fully_approved') return AppTheme.success;
    if (status == 'rejected' || status == 'cancelled') return AppTheme.error;
    return AppTheme.warning;
  }

  String _getStatusText(String status) {
    if (status == 'fully_approved') return 'อนุมัติแล้ว';
    if (status == 'rejected') return 'ปฏิเสธ';
    if (status == 'cancelled') return 'ยกเลิก';
    if (status == 'pending') return 'รอผู้แทนยืนยัน';
    if (status == 'approved') return 'รอหัวหน้าแผนก';
    if (status == 'director_approved') return 'รอ ผอ. อนุมัติ';
    return status;
  }
}
