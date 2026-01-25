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

    final success = await provider.submitRequest(data);

    if (mounted) {
      setState(() => _isLoading = false);
      if (success) {
        _showSnackBar('ส่งใบลาเรียบร้อยแล้ว', AppTheme.success);
        Navigator.pop(context, true);
      } else {
        _showSnackBar('เกิดข้อผิดพลาด กรุณาลองใหม่', AppTheme.error);
      }
    }
  }

  void _showSnackBar(String message, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: color,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
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
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              _buildStepTitle('1. เลือกประเภทการลา'),
              const SizedBox(height: 12),
              _buildTypeSelector(leaveProvider.leaveTypes),

              const SizedBox(height: 32),
              _buildStepTitle('2. ช่วงเวลาที่ต้องการลา'),
              const SizedBox(height: 12),
              _buildDateSection(),

              const SizedBox(height: 32),
              _buildStepTitle('3. รายละเอียดเพิ่มเติม'),
              const SizedBox(height: 12),
              _buildFormFields(),

              const SizedBox(height: 48),
              ElevatedButton(
                onPressed: _isLoading ? null : _submit,
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 20),
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
                    : const Text('ส่งคำขอลา', style: TextStyle(fontSize: 18)),
              ),
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStepTitle(String title) {
    return Text(
      title,
      style: Theme.of(context).textTheme.titleMedium?.copyWith(
        color: AppTheme.primary,
        fontWeight: FontWeight.bold,
      ),
    );
  }

  Widget _buildTypeSelector(List<LeaveType> types) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppTheme.border),
      ),
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: DropdownButtonHideUnderline(
        child: DropdownButtonFormField<LeaveType>(
          decoration: const InputDecoration(
            border: InputBorder.none,
            enabledBorder: InputBorder.none,
            focusedBorder: InputBorder.none,
            labelText: 'ประเภทการลา',
          ),
          value: _selectedType,
          items: types.map((type) {
            return DropdownMenuItem(value: type, child: Text(type.name));
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
          ),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: _buildDateCard(
            label: 'ถึงวันที่',
            date: _endDate,
            onTap: () => _selectDate(false),
          ),
        ),
      ],
    );
  }

  Widget _buildDateCard({
    required String label,
    DateTime? date,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppTheme.border),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label, style: Theme.of(context).textTheme.bodyMedium),
            const SizedBox(height: 8),
            Row(
              children: [
                const Icon(
                  Icons.calendar_month_rounded,
                  size: 18,
                  color: AppTheme.primary,
                ),
                const SizedBox(width: 8),
                Text(
                  date != null
                      ? DateFormat('dd/MM/yy').format(date)
                      : 'ระบุวันที่',
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    color: date != null ? AppTheme.textMain : Colors.grey[400],
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
        TextFormField(
          controller: _reasonController,
          decoration: const InputDecoration(
            labelText: 'เหตุผลการลา',
            prefixIcon: Icon(Icons.description_outlined),
            hintText: 'ระบุเหตุผลเพื่อประกอบการพิจารณา',
          ),
          maxLines: 3,
          validator: (val) => val!.isEmpty ? 'กรุณาระบุเหตุผล' : null,
        ),
        const SizedBox(height: 16),
        TextFormField(
          controller: _addressController,
          decoration: const InputDecoration(
            labelText: 'สถานที่ติดต่อ / เบอร์โทร',
            prefixIcon: Icon(Icons.location_on_outlined),
            hintText: 'เพื่อติดต่อกรณีฉุกเฉิน',
          ),
        ),
      ],
    );
  }
}
