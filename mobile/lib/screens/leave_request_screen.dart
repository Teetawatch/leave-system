import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../config/app_theme.dart';
import '../models/leave_type_model.dart';
import '../services/api_service.dart';

class LeaveRequestScreen extends StatefulWidget {
  const LeaveRequestScreen({super.key});

  @override
  State<LeaveRequestScreen> createState() => _LeaveRequestScreenState();
}

class _LeaveRequestScreenState extends State<LeaveRequestScreen> {
  final ApiService _apiService = ApiService();
  final _formKey = GlobalKey<FormState>();

  List<LeaveType> _leaveTypes = [];
  LeaveType? _selectedType;
  DateTime? _startDate;
  DateTime? _endDate;
  final TextEditingController _reasonController = TextEditingController();
  final TextEditingController _addressController = TextEditingController();

  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _fetchLeaveTypes();
  }

  Future<void> _fetchLeaveTypes() async {
    try {
      final response = await _apiService.getLeaveTypes();
      if (response.data['success']) {
        final List data = response.data['data'];
        setState(() {
          _leaveTypes = data.map((json) => LeaveType.fromJson(json)).toList();
        });
      }
    } catch (e) {
      // Handle error
    }
  }

  Future<void> _selectDate(bool isStart) async {
    final picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(2024),
      lastDate: DateTime(2026),
      builder: (context, child) {
        return Theme(
          data: AppTheme.lightTheme.copyWith(
            colorScheme: AppTheme.lightTheme.colorScheme.copyWith(
              primary: AppTheme.primary,
              onPrimary: Colors.white,
            ),
          ),
          child: child!,
        );
      },
    );

    if (picked != null) {
      setState(() {
        if (isStart) {
          _startDate = picked;
          if (_endDate == null || _endDate!.isBefore(picked)) {
            _endDate = picked;
          }
        } else {
          _endDate = picked;
        }
      });
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_startDate == null || _endDate == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('กรุณาเลือกวันที่'),
          backgroundColor: AppTheme.error,
        ),
      );
      return;
    }

    setState(() => _isLoading = true);
    FocusScope.of(context).unfocus();

    try {
      final data = {
        'leave_type_id': _selectedType!.id,
        'start_date': DateFormat('yyyy-MM-dd').format(_startDate!),
        'end_date': DateFormat('yyyy-MM-dd').format(_endDate!),
        'reason': _reasonController.text,
        'contact_address': {'house': _addressController.text, 'province': '-'},
      };

      final response = await _apiService.submitLeaveRequest(data);

      if (mounted) {
        setState(() => _isLoading = false);
        if (response.data['success']) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('ส่งใบลาเรียบร้อยแล้ว'),
              backgroundColor: Color(0xFF10B981), // Green
            ),
          );
          Navigator.pop(context, true);
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('เกิดข้อผิดพลาดในการส่งใบลา'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(title: const Text('ยื่นใบลา')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Card(
                elevation: 2,
                child: Padding(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    children: [
                      DropdownButtonFormField<LeaveType>(
                        decoration: const InputDecoration(
                          labelText: 'ประเภทการลา',
                          prefixIcon: Icon(Icons.category_outlined),
                        ),
                        value: _selectedType,
                        items: _leaveTypes.map((type) {
                          return DropdownMenuItem(
                            value: type,
                            child: Text(type.name),
                          );
                        }).toList(),
                        onChanged: (val) {
                          setState(() => _selectedType = val);
                        },
                        validator: (val) =>
                            val == null ? 'กรุณาเลือกประเภทการลา' : null,
                      ),
                      const SizedBox(height: 20),
                      Row(
                        children: [
                          Expanded(
                            child: InkWell(
                              onTap: () => _selectDate(true),
                              borderRadius: BorderRadius.circular(12),
                              child: IgnorePointer(
                                child: InputDecorator(
                                  decoration: const InputDecoration(
                                    labelText: 'จากวันที่',
                                    prefixIcon: Icon(
                                      Icons.calendar_today_outlined,
                                    ),
                                  ),
                                  child: Text(
                                    _startDate != null
                                        ? DateFormat(
                                            'dd/MM/yyyy',
                                          ).format(_startDate!)
                                        : '-',
                                    style: _startDate != null
                                        ? const TextStyle(
                                            fontWeight: FontWeight.bold,
                                          )
                                        : null,
                                  ),
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: InkWell(
                              onTap: () => _selectDate(false),
                              borderRadius: BorderRadius.circular(12),
                              child: IgnorePointer(
                                child: InputDecorator(
                                  decoration: const InputDecoration(
                                    labelText: 'ถึงวันที่',
                                    prefixIcon: Icon(
                                      Icons.calendar_today_outlined,
                                    ),
                                  ),
                                  child: Text(
                                    _endDate != null
                                        ? DateFormat(
                                            'dd/MM/yyyy',
                                          ).format(_endDate!)
                                        : '-',
                                    style: _endDate != null
                                        ? const TextStyle(
                                            fontWeight: FontWeight.bold,
                                          )
                                        : null,
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),

              Card(
                elevation: 2,
                child: Padding(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    children: [
                      TextFormField(
                        controller: _reasonController,
                        decoration: const InputDecoration(
                          labelText: 'เหตุผลการลา',
                          prefixIcon: Icon(Icons.notes_rounded),
                          alignLabelWithHint: true,
                        ),
                        maxLines: 3,
                        validator: (val) =>
                            val!.isEmpty ? 'กรุณาระบุเหตุผล' : null,
                      ),
                      const SizedBox(height: 20),
                      TextFormField(
                        controller: _addressController,
                        decoration: const InputDecoration(
                          labelText: 'สถานที่ติดต่อ (ถ้ามี)',
                          prefixIcon: Icon(Icons.location_on_outlined),
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              const SizedBox(height: 32),

              ElevatedButton(
                onPressed: _isLoading ? null : _submit,
                child: _isLoading
                    ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(
                          color: Colors.white,
                          strokeWidth: 2,
                        ),
                      )
                    : const Text('ส่งใบลา'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
