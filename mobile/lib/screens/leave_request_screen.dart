import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../config/app_theme.dart';
import '../models/leave_type_model.dart';
import '../providers/leave_provider.dart';

class LeaveRequestScreen extends StatefulWidget {
  const LeaveRequestScreen({super.key});

  @override
  State<LeaveRequestScreen> createState() => _LeaveRequestScreenState();
}

class _LeaveRequestScreenState extends State<LeaveRequestScreen> {
  final _formKey = GlobalKey<FormState>();

  LeaveType? _selectedType;
  DateTime? _startDate;
  DateTime? _endDate;
  final TextEditingController _reasonController = TextEditingController();
  final TextEditingController _addressController = TextEditingController();

  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<LeaveProvider>(context, listen: false).fetchLeaveTypes();
    });
  }

  Future<void> _selectDate(bool isStart) async {
    final picked = await showDatePicker(
      context: context,
      initialDate: isStart
          ? (_startDate ?? DateTime.now())
          : (_endDate ?? (_startDate ?? DateTime.now())),
      firstDate: DateTime.now().subtract(const Duration(days: 30)),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: AppTheme.primary,
              onPrimary: Colors.white,
              onSurface: AppTheme.textMain,
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
          if (_endDate != null && _endDate!.isBefore(picked)) {
            _endDate = picked;
          }
        } else {
          _endDate = picked;
          if (_startDate != null && _startDate!.isAfter(picked)) {
            _startDate = picked;
          }
        }
      });
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_startDate == null || _endDate == null) {
      _showSnackBar('กรุณาเลือกวันที่ลางาน', AppTheme.error);
      return;
    }

    setState(() => _isLoading = true);
    FocusScope.of(context).unfocus();

    final provider = Provider.of<LeaveProvider>(context, listen: false);

    final data = {
      'leave_type_id': _selectedType!.id,
      'start_date': DateFormat('yyyy-MM-dd').format(_startDate!),
      'end_date': DateFormat('yyyy-MM-dd').format(_endDate!),
      'reason': _reasonController.text,
      'contact_address': _addressController.text,
    };

    final Map<String, dynamic> response = await provider.submitRequest(data);

    if (mounted) {
      setState(() => _isLoading = false);
      final bool isSuccess = response['success'] == true;
      final String message =
          response['message'] ??
          (isSuccess ? 'ส่งใบลาเรียบร้อยแล้ว' : 'เกิดข้อผิดพลาด');

      if (isSuccess) {
        _showSnackBar(message, AppTheme.success);
        Navigator.pop(context, true);
      } else {
        _showSnackBar(message, AppTheme.error);
      }
    }
  }

  void _showSnackBar(String message, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: color,
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.all(24),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final leaveProvider = Provider.of<LeaveProvider>(context);

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('ยื่นใบขอลา'),
        backgroundColor: Colors.transparent,
        surfaceTintColor: Colors.transparent,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Header description
              Text(
                'ระบุรายละเอียดการลา',
                style: Theme.of(context).textTheme.displaySmall?.copyWith(
                  fontWeight: FontWeight.w900,
                  fontSize: 28,
                  letterSpacing: -0.5,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                'กรุณาตรวจสอบข้อมูลให้ถูกต้องก่อนส่งคำขอ',
                style: Theme.of(
                  context,
                ).textTheme.bodyMedium?.copyWith(color: AppTheme.textSub),
              ),
              const SizedBox(height: 32),

              // Step 1: Leave Type
              _buildSectionHeader('ประเภทการลา', Icons.category_rounded),
              const SizedBox(height: 12),
              _buildTypeSelector(leaveProvider.leaveTypes),

              const SizedBox(height: 24),

              // Step 2: Date Selector
              _buildSectionHeader(
                'ช่วงเวลาที่ต้องการลา',
                Icons.date_range_rounded,
              ),
              const SizedBox(height: 12),
              _buildDateSection(),

              const SizedBox(height: 24),

              // Step 3: Details
              _buildSectionHeader(
                'รายละเอียดเพิ่มเติม',
                Icons.edit_note_rounded,
              ),
              const SizedBox(height: 12),
              _buildFormFields(),

              const SizedBox(height: 40),

              // Submission Button
              SizedBox(
                height: 64,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primary,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(24),
                    ),
                    elevation: 8,
                    shadowColor: AppTheme.primary.withOpacity(0.4),
                  ),
                  child: _isLoading
                      ? const SizedBox(
                          width: 24,
                          height: 24,
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                      : Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Text(
                              'ส่งใบขอลา',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.w800,
                                letterSpacing: 0.5,
                              ),
                            ),
                            const SizedBox(width: 8),
                            Icon(
                              Icons.send_rounded,
                              size: 20,
                              color: Colors.white.withOpacity(0.9),
                            ),
                          ],
                        ),
                ),
              ),
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title, IconData icon) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: AppTheme.primary.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, size: 18, color: AppTheme.primary),
        ),
        const SizedBox(width: 12),
        Text(
          title,
          style: Theme.of(context).textTheme.titleMedium?.copyWith(
            fontWeight: FontWeight.w800,
            color: AppTheme.textMain,
          ),
        ),
      ],
    );
  }

  Widget _buildTypeSelector(List<LeaveType> types) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: AppTheme.border.withOpacity(0.5)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 4),
      child: DropdownButtonHideUnderline(
        child: DropdownButtonFormField<LeaveType>(
          decoration: const InputDecoration(
            border: InputBorder.none,
            enabledBorder: InputBorder.none,
            focusedBorder: InputBorder.none,
            filled: false,
            contentPadding: EdgeInsets.zero,
          ),
          hint: const Text('เลือกประเภทการลา'),
          value: _selectedType,
          items: types.map((type) {
            return DropdownMenuItem(
              value: type,
              child: Text(
                type.name,
                style: const TextStyle(fontWeight: FontWeight.w600),
              ),
            );
          }).toList(),
          onChanged: (val) => setState(() => _selectedType = val),
          validator: (val) => val == null ? 'กรุณาเลือกประเภทการลา' : null,
        ),
      ),
    );
  }

  Widget _buildDateSection() {
    return Row(
      children: [
        Expanded(
          child: _buildDateCard(
            label: 'เริ่มวันที่',
            date: _startDate,
            onTap: () => _selectDate(true),
            accentColor: AppTheme.primary,
          ),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: _buildDateCard(
            label: 'สิ้นสุดวันที่',
            date: _endDate,
            onTap: () => _selectDate(false),
            accentColor: AppTheme.secondary,
          ),
        ),
      ],
    );
  }

  Widget _buildDateCard({
    required String label,
    DateTime? date,
    required VoidCallback onTap,
    required Color accentColor,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(28),
          border: Border.all(color: AppTheme.border.withOpacity(0.5)),
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
            Text(
              label,
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                color: AppTheme.textSub,
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(6),
                  decoration: BoxDecoration(
                    color: accentColor.withOpacity(0.1),
                    shape: BoxShape.circle,
                  ),
                  child: Icon(
                    Icons.calendar_month_rounded,
                    size: 16,
                    color: accentColor,
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    date != null
                        ? DateFormat('dd/MM/yy').format(date)
                        : 'ระบุวันที่',
                    style: TextStyle(
                      fontWeight: FontWeight.w900,
                      fontSize: 16,
                      color: date != null
                          ? AppTheme.textMain
                          : AppTheme.textSub.withOpacity(0.4),
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildFormFields() {
    return Column(
      children: [
        Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: AppTheme.border.withOpacity(0.5)),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.02),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: TextFormField(
            controller: _reasonController,
            decoration: const InputDecoration(
              labelText: 'เหตุผลการลา',
              hintText: 'ระบุเหตุผลเพื่อประกอบการพิจารณา',
              border: InputBorder.none,
              enabledBorder: InputBorder.none,
              focusedBorder: InputBorder.none,
              contentPadding: EdgeInsets.all(20),
            ),
            maxLines: 3,
            validator: (val) => val!.isEmpty ? 'กรุณาระบุเหตุผล' : null,
          ),
        ),
        const SizedBox(height: 16),
        Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: AppTheme.border.withOpacity(0.5)),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.02),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: TextFormField(
            controller: _addressController,
            decoration: const InputDecoration(
              labelText: 'ระบุที่อยู่ / เบอร์โทรติดต่อ',
              hintText: 'สำหรับติดต่อกรณีฉุกเฉิน',
              border: InputBorder.none,
              enabledBorder: InputBorder.none,
              focusedBorder: InputBorder.none,
              contentPadding: EdgeInsets.all(20),
            ),
          ),
        ),
      ],
    );
  }
}
