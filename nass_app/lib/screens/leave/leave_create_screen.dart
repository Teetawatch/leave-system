import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../config/api_config.dart';
import '../../config/app_theme.dart';
import '../../models/leave_type.dart';
import '../../services/api_service.dart';

class LeaveCreateScreen extends StatefulWidget {
  const LeaveCreateScreen({super.key});

  @override
  State<LeaveCreateScreen> createState() => _LeaveCreateScreenState();
}

class _LeaveCreateScreenState extends State<LeaveCreateScreen> {
  final _formKey = GlobalKey<FormState>();
  final ApiService _api = ApiService();
  final _reasonController = TextEditingController();

  List<LeaveType> _leaveTypes = [];
  LeaveType? _selectedType;
  DateTime? _startDate;
  DateTime? _endDate;
  bool _isLoading = false;
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    _loadLeaveTypes();
  }

  @override
  void dispose() {
    _reasonController.dispose();
    super.dispose();
  }

  Future<void> _loadLeaveTypes() async {
    setState(() => _isLoading = true);
    try {
      final response = await _api.get(ApiConfig.leaveTypes);
      if (response['success'] == true) {
        final data = response['data'] as List;
        _leaveTypes = data.map((t) => LeaveType.fromJson(t)).toList();
      }
    } catch (e) {
      debugPrint('Load leave types error: $e');
    }
    if (mounted) setState(() => _isLoading = false);
  }

  Future<void> _selectDate(bool isStart) async {
    final now = DateTime.now();
    final date = await showDatePicker(
      context: context,
      initialDate: isStart ? (_startDate ?? now) : (_endDate ?? _startDate ?? now),
      firstDate: now.subtract(const Duration(days: 30)),
      lastDate: now.add(const Duration(days: 365)),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(primary: AppTheme.primary),
          ),
          child: child!,
        );
      },
    );
    if (date != null) {
      setState(() {
        if (isStart) {
          _startDate = date;
          if (_endDate != null && _endDate!.isBefore(date)) {
            _endDate = date;
          }
        } else {
          _endDate = date;
        }
      });
    }
  }

  int get _totalDays {
    if (_startDate == null || _endDate == null) return 0;
    return _endDate!.difference(_startDate!).inDays + 1;
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedType == null || _startDate == null || _endDate == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('กรุณากรอกข้อมูลให้ครบถ้วน'), backgroundColor: AppTheme.error),
      );
      return;
    }

    setState(() => _isSubmitting = true);
    try {
      final response = await _api.post(ApiConfig.leaveRequests, body: {
        'leave_type_id': _selectedType!.id,
        'start_date': DateFormat('yyyy-MM-dd').format(_startDate!),
        'end_date': DateFormat('yyyy-MM-dd').format(_endDate!),
        'reason': _reasonController.text.trim(),
      });

      if (response['success'] == true && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('ส่งคำขอลาเรียบร้อยแล้ว'), backgroundColor: AppTheme.success),
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
    if (mounted) setState(() => _isSubmitting = false);
  }

  @override
  Widget build(BuildContext context) {
    final dateFormat = DateFormat('d MMMM yyyy', 'th');

    return Scaffold(
      appBar: AppBar(title: Text('ยื่นใบลา', style: AppTheme.heading(18))),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
          : SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Leave Type
                    Text('ประเภทการลา', style: AppTheme.heading(15)),
                    const SizedBox(height: 8),
                    Container(
                      decoration: BoxDecoration(
                        color: AppTheme.background,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: AppTheme.divider),
                      ),
                      child: DropdownButtonFormField<LeaveType>(
                        initialValue: _selectedType,
                        decoration: const InputDecoration(
                          contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                          border: InputBorder.none,
                          hintText: 'เลือกประเภทการลา',
                        ),
                        items: _leaveTypes.map((t) {
                          return DropdownMenuItem(
                            value: t,
                            child: Text(t.name),
                          );
                        }).toList(),
                        onChanged: (val) => setState(() => _selectedType = val),
                        validator: (val) => val == null ? 'กรุณาเลือกประเภทการลา' : null,
                      ),
                    ),

                    // Max days info
                    if (_selectedType != null && _selectedType!.maxDaysPerYear != null)
                      Padding(
                        padding: const EdgeInsets.only(top: 6),
                        child: Text(
                          'สิทธิ์ลาสูงสุด ${_selectedType!.maxDaysPerYear} วัน/ปี',
                          style: AppTheme.body(12, color: AppTheme.textMuted),
                        ),
                      ),

                    const SizedBox(height: 20),

                    // Date Range
                    Row(
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('วันเริ่มต้น', style: AppTheme.heading(15)),
                              const SizedBox(height: 8),
                              InkWell(
                                onTap: () => _selectDate(true),
                                borderRadius: BorderRadius.circular(12),
                                child: Container(
                                  padding: const EdgeInsets.all(14),
                                  decoration: BoxDecoration(
                                    color: AppTheme.background,
                                    borderRadius: BorderRadius.circular(12),
                                    border: Border.all(color: AppTheme.divider),
                                  ),
                                  child: Row(
                                    children: [
                                      const Icon(Icons.calendar_today_rounded, size: 18, color: AppTheme.primary),
                                      const SizedBox(width: 8),
                                      Expanded(
                                        child: Text(
                                          _startDate != null ? dateFormat.format(_startDate!) : 'เลือกวันที่',
                                          style: TextStyle(
                                            fontSize: 14,
                                            color: _startDate != null ? AppTheme.textPrimary : AppTheme.textMuted,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('วันสิ้นสุด', style: AppTheme.heading(15)),
                              const SizedBox(height: 8),
                              InkWell(
                                onTap: () => _selectDate(false),
                                borderRadius: BorderRadius.circular(12),
                                child: Container(
                                  padding: const EdgeInsets.all(14),
                                  decoration: BoxDecoration(
                                    color: AppTheme.background,
                                    borderRadius: BorderRadius.circular(12),
                                    border: Border.all(color: AppTheme.divider),
                                  ),
                                  child: Row(
                                    children: [
                                      const Icon(Icons.calendar_today_rounded, size: 18, color: AppTheme.primary),
                                      const SizedBox(width: 8),
                                      Expanded(
                                        child: Text(
                                          _endDate != null ? dateFormat.format(_endDate!) : 'เลือกวันที่',
                                          style: TextStyle(
                                            fontSize: 14,
                                            color: _endDate != null ? AppTheme.textPrimary : AppTheme.textMuted,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),

                    // Total days
                    if (_totalDays > 0)
                      Padding(
                        padding: const EdgeInsets.only(top: 10),
                        child: Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: AppTheme.primary.withValues(alpha: 0.06),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Icon(Icons.date_range_rounded, size: 18, color: AppTheme.primary),
                              const SizedBox(width: 6),
                              Text(
                                'รวม $_totalDays วัน',
                                style: AppTheme.heading(15, color: AppTheme.primary),
                              ),
                            ],
                          ),
                        ),
                      ),

                    const SizedBox(height: 20),

                    // Reason
                    Text('เหตุผลการลา', style: AppTheme.heading(15)),
                    const SizedBox(height: 8),
                    TextFormField(
                      controller: _reasonController,
                      maxLines: 4,
                      decoration: InputDecoration(
                        hintText: 'ระบุเหตุผลการลา...',
                        filled: true,
                        fillColor: AppTheme.background,
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: BorderSide(color: AppTheme.divider),
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: BorderSide(color: AppTheme.divider),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: const BorderSide(color: AppTheme.primary, width: 2),
                        ),
                      ),
                      validator: (val) {
                        if (val == null || val.trim().isEmpty) return 'กรุณาระบุเหตุผล';
                        return null;
                      },
                    ),

                    const SizedBox(height: 32),

                    // Submit
                    Container(
                      height: 56,
                      decoration: BoxDecoration(
                        gradient: _isSubmitting ? null : AppTheme.primaryGradient,
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: _isSubmitting ? null : [
                          BoxShadow(
                            color: AppTheme.primary.withValues(alpha: 0.3),
                            blurRadius: 12,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: ElevatedButton.icon(
                        onPressed: _isSubmitting ? null : _submit,
                        icon: _isSubmitting
                            ? const SizedBox(
                                width: 20,
                                height: 20,
                                child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                              )
                            : const Icon(Icons.send_rounded),
                        label: Text(_isSubmitting ? 'กำลังส่ง...' : 'ส่งคำขอลา', style: GoogleFonts.prompt(fontWeight: FontWeight.w600, fontSize: 16)),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.transparent,
                          shadowColor: Colors.transparent,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}
