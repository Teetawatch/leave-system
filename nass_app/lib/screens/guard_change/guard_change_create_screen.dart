import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../config/api_config.dart';
import '../../config/app_theme.dart';
import '../../services/api_service.dart';

class GuardChangeCreateScreen extends StatefulWidget {
  const GuardChangeCreateScreen({super.key});

  @override
  State<GuardChangeCreateScreen> createState() => _GuardChangeCreateScreenState();
}

class _GuardChangeCreateScreenState extends State<GuardChangeCreateScreen> {
  final _formKey = GlobalKey<FormState>();
  final ApiService _api = ApiService();
  final _remarksController = TextEditingController();

  List<Map<String, dynamic>> _users = [];
  Map<String, dynamic>? _selectedUser;
  String? _selectedPosition;
  DateTime? _dutyDate;
  bool _isLoading = false;
  bool _isSubmitting = false;

  final _positions = [
    {'value': 'senior_duty_officer', 'label': 'นายทหารเวรอาวุโส'},
    {'value': 'duty_officer', 'label': 'นายทหารเวร'},
    {'value': 'assistant_duty_officer', 'label': 'ผู้ช่วยนายทหารเวร'},
  ];

  @override
  void initState() {
    super.initState();
    _loadUsers();
  }

  @override
  void dispose() {
    _remarksController.dispose();
    super.dispose();
  }

  Future<void> _loadUsers() async {
    setState(() => _isLoading = true);
    try {
      final response = await _api.get(ApiConfig.guardChangeUsers);
      if (response['success'] == true && response['data'] is List) {
        _users = (response['data'] as List).map((u) => Map<String, dynamic>.from(u)).toList();
      }
    } catch (e) {
      debugPrint('Load users error: $e');
    }
    if (mounted) setState(() => _isLoading = false);
  }

  Future<void> _selectDate() async {
    final now = DateTime.now();
    final date = await showDatePicker(
      context: context,
      initialDate: _dutyDate ?? now,
      firstDate: now,
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
    if (date != null) setState(() => _dutyDate = date);
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedUser == null || _selectedPosition == null || _dutyDate == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('กรุณากรอกข้อมูลให้ครบถ้วน'), backgroundColor: AppTheme.error),
      );
      return;
    }

    setState(() => _isSubmitting = true);
    try {
      final response = await _api.post(ApiConfig.guardChangeRequests, body: {
        'replacement_user_id': _selectedUser!['id'],
        'duty_position': _selectedPosition,
        'duty_date': DateFormat('yyyy-MM-dd').format(_dutyDate!),
        'remarks': _remarksController.text.trim().isEmpty ? null : _remarksController.text.trim(),
      });

      if (response['success'] == true && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('ส่งคำขอเปลี่ยนเวรเรียบร้อยแล้ว'), backgroundColor: AppTheme.success),
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
      appBar: AppBar(title: Text('ขอเปลี่ยนเวร', style: AppTheme.heading(18))),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
          : SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Duty Position
                    Text('ตำแหน่งเวร', style: AppTheme.heading(15)),
                    const SizedBox(height: 8),
                    Container(
                      decoration: BoxDecoration(
                        color: AppTheme.background,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: AppTheme.divider),
                      ),
                      child: DropdownButtonFormField<String>(
                        initialValue: _selectedPosition,
                        decoration: const InputDecoration(
                          contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                          border: InputBorder.none,
                          hintText: 'เลือกตำแหน่งเวร',
                        ),
                        items: _positions.map((p) {
                          return DropdownMenuItem(value: p['value'] as String, child: Text(p['label'] as String));
                        }).toList(),
                        onChanged: (val) => setState(() => _selectedPosition = val),
                        validator: (val) => val == null ? 'กรุณาเลือกตำแหน่งเวร' : null,
                      ),
                    ),

                    const SizedBox(height: 20),

                    // Duty Date
                    Text('วันที่เวร', style: AppTheme.heading(15)),
                    const SizedBox(height: 8),
                    InkWell(
                      onTap: _selectDate,
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
                            const SizedBox(width: 10),
                            Text(
                              _dutyDate != null ? dateFormat.format(_dutyDate!) : 'เลือกวันที่เวร',
                              style: TextStyle(
                                fontSize: 15,
                                color: _dutyDate != null ? AppTheme.textPrimary : AppTheme.textMuted,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),

                    const SizedBox(height: 20),

                    // Replacement User
                    Text('ผู้รับเปลี่ยนเวร', style: AppTheme.heading(15)),
                    const SizedBox(height: 8),
                    Container(
                      decoration: BoxDecoration(
                        color: AppTheme.background,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: AppTheme.divider),
                      ),
                      child: DropdownButtonFormField<Map<String, dynamic>>(
                        initialValue: _selectedUser,
                        isExpanded: true,
                        decoration: const InputDecoration(
                          contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                          border: InputBorder.none,
                          hintText: 'เลือกผู้รับเปลี่ยนเวร',
                        ),
                        items: _users.map((u) {
                          final rank = u['rank'] ?? '';
                          final name = u['name'] ?? '';
                          final displayName = rank.isNotEmpty ? '$rank $name' : name;
                          return DropdownMenuItem(
                            value: u,
                            child: Text(displayName, overflow: TextOverflow.ellipsis),
                          );
                        }).toList(),
                        onChanged: (val) => setState(() => _selectedUser = val),
                        validator: (val) => val == null ? 'กรุณาเลือกผู้รับเปลี่ยนเวร' : null,
                      ),
                    ),

                    const SizedBox(height: 20),

                    // Remarks
                    Text('หมายเหตุ (ถ้ามี)', style: AppTheme.heading(15)),
                    const SizedBox(height: 8),
                    TextFormField(
                      controller: _remarksController,
                      maxLines: 3,
                      decoration: InputDecoration(
                        hintText: 'ระบุหมายเหตุ...',
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
                        label: Text(_isSubmitting ? 'กำลังส่ง...' : 'ส่งคำขอเปลี่ยนเวร', style: GoogleFonts.prompt(fontWeight: FontWeight.w600, fontSize: 16)),
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
