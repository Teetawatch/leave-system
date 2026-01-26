import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../models/leave_type_model.dart';
import '../providers/leave_provider.dart';

class LeaveRequestScreen extends StatefulWidget {
  const LeaveRequestScreen({super.key});

  @override
  State<LeaveRequestScreen> createState() => _LeaveRequestScreenState();
}

class _LeaveRequestScreenState extends State<LeaveRequestScreen>
    with SingleTickerProviderStateMixin {
  final _formKey = GlobalKey<FormState>();
  late AnimationController _animationController;

  LeaveType? _selectedType;
  DateTime? _startDate;
  DateTime? _endDate;
  final TextEditingController _reasonController = TextEditingController();
  final TextEditingController _addressController = TextEditingController();

  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1000),
    )..forward();

    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<LeaveProvider>(context, listen: false).fetchLeaveTypes();
    });
  }

  @override
  void dispose() {
    _animationController.dispose();
    _reasonController.dispose();
    _addressController.dispose();
    super.dispose();
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
        content: Text(
          message,
          style: const TextStyle(fontWeight: FontWeight.w600),
        ),
        backgroundColor: color,
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.all(24),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final leaveProvider = Provider.of<LeaveProvider>(context);
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: Colors.white,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text(
          'ยื่นใบขอลา',
          style: TextStyle(fontWeight: FontWeight.w900),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: AppTheme.textMain,
        elevation: 0,
      ),
      body: Stack(
        children: [
          // Background components
          Container(
            height: size.height,
            width: size.width,
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [Color(0xFFF8FAFC), Colors.white, Color(0xFFF0F4FF)],
              ),
            ),
          ),
          _buildFloatingCircle(
            top: -50,
            right: -50,
            size: 200,
            color: AppTheme.primary.withOpacity(0.05),
          ),
          _buildFloatingCircle(
            bottom: 100,
            left: -80,
            size: 300,
            color: AppTheme.secondary.withOpacity(0.04),
          ),

          SafeArea(
            child: FadeTransition(
              opacity: _animationController,
              child: SingleChildScrollView(
                physics: const BouncingScrollPhysics(),
                padding: const EdgeInsets.symmetric(
                  horizontal: 28,
                  vertical: 20,
                ),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      _buildHeaderSection(),
                      const SizedBox(height: 32),

                      // Main Form Card
                      Container(
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(32),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.03),
                              blurRadius: 30,
                              offset: const Offset(0, 15),
                            ),
                          ],
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(32),
                          child: BackdropFilter(
                            filter: ImageFilter.blur(sigmaX: 5, sigmaY: 5),
                            child: Padding(
                              padding: const EdgeInsets.all(24),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  _buildSectionLabel(
                                    'ประเภทการลา',
                                    Icons.category_rounded,
                                  ),
                                  const SizedBox(height: 12),
                                  _buildTypeSelector(leaveProvider.leaveTypes),
                                  const SizedBox(height: 24),

                                  _buildSectionLabel(
                                    'ระยะเวลา',
                                    Icons.date_range_rounded,
                                  ),
                                  const SizedBox(height: 12),
                                  _buildDateRow(),
                                  const SizedBox(height: 24),

                                  _buildSectionLabel(
                                    'รายละเอียดและที่ติดต่อ',
                                    Icons.edit_note_rounded,
                                  ),
                                  const SizedBox(height: 12),
                                  _buildFormFields(),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ),

                      const SizedBox(height: 40),

                      // Gradient Submit Button
                      _buildSubmitButton(),
                      const SizedBox(height: 40),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFloatingCircle({
    required double size,
    required Color color,
    double? top,
    double? bottom,
    double? left,
    double? right,
  }) {
    return Positioned(
      top: top,
      bottom: bottom,
      left: left,
      right: right,
      child: Container(
        width: size,
        height: size,
        decoration: BoxDecoration(shape: BoxShape.circle, color: color),
      ),
    );
  }

  Widget _buildHeaderSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'ระบุรายละเอียดการลา',
          style: TextStyle(
            fontSize: 26,
            fontWeight: FontWeight.w900,
            color: AppTheme.textMain,
            letterSpacing: -0.5,
          ),
        ),
        const SizedBox(height: 8),
        Text(
          'เจ้าหน้าที่จะพิจารณาคำขอของคุณในลำดับถัดไป',
          style: TextStyle(
            color: AppTheme.textSub.withOpacity(0.7),
            fontSize: 14,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }

  Widget _buildSectionLabel(String text, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 18, color: AppTheme.primary),
        const SizedBox(width: 8),
        Text(
          text,
          style: const TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.w800,
            color: AppTheme.textMain,
          ),
        ),
      ],
    );
  }

  Widget _buildTypeSelector(List<LeaveType> types) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      decoration: BoxDecoration(
        color: AppTheme.primaryLight.withOpacity(0.4),
        borderRadius: BorderRadius.circular(20),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButtonFormField<LeaveType>(
          decoration: const InputDecoration(
            border: InputBorder.none,
            filled: false,
            contentPadding: EdgeInsets.zero,
          ),
          hint: const Text(
            'เลือกประเภทการลา',
            style: TextStyle(fontWeight: FontWeight.w500, fontSize: 14),
          ),
          value: _selectedType,
          items: types.map((type) {
            return DropdownMenuItem(
              value: type,
              child: Text(
                type.name,
                style: const TextStyle(
                  fontWeight: FontWeight.w700,
                  color: AppTheme.textMain,
                ),
              ),
            );
          }).toList(),
          onChanged: (val) => setState(() => _selectedType = val),
          validator: (val) => val == null ? 'กรุณาเลือกประเภทการลา' : null,
        ),
      ),
    );
  }

  Widget _buildDateRow() {
    return Row(
      children: [
        Expanded(
          child: _buildDateItem(
            'เริ่มวันที่',
            _startDate,
            () => _selectDate(true),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: _buildDateItem(
            'สิ้นสุดวันที่',
            _endDate,
            () => _selectDate(false),
          ),
        ),
      ],
    );
  }

  Widget _buildDateItem(String label, DateTime? date, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: AppTheme.primaryLight.withOpacity(0.4),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              label,
              style: const TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w700,
                color: AppTheme.textSub,
              ),
            ),
            const SizedBox(height: 6),
            Text(
              date != null ? DateFormat('dd/MM/yy').format(date) : 'ระบุวันที่',
              style: TextStyle(
                fontWeight: FontWeight.w800,
                fontSize: 14,
                color: date != null
                    ? AppTheme.textMain
                    : AppTheme.textSub.withOpacity(0.5),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildFormFields() {
    return Column(
      children: [
        _buildTextField(
          controller: _reasonController,
          hint: 'ระบุเหตุผลการลา...',
          maxLines: 3,
        ),
        const SizedBox(height: 12),
        _buildTextField(
          controller: _addressController,
          hint: 'ระบุที่อยู่ / เบอร์โทรติดต่อฉุกเฉิน',
          maxLines: 2,
        ),
      ],
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String hint,
    int maxLines = 1,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: AppTheme.primaryLight.withOpacity(0.4),
        borderRadius: BorderRadius.circular(20),
      ),
      child: TextFormField(
        controller: controller,
        maxLines: maxLines,
        style: const TextStyle(
          fontWeight: FontWeight.w600,
          color: AppTheme.textMain,
          fontSize: 14,
        ),
        decoration: InputDecoration(
          hintText: hint,
          hintStyle: TextStyle(
            color: AppTheme.textSub.withOpacity(0.5),
            fontSize: 14,
          ),
          border: InputBorder.none,
          contentPadding: const EdgeInsets.all(16),
        ),
        validator: (val) => val!.isEmpty ? 'กรุณากรอกข้อมูล' : null,
      ),
    );
  }

  Widget _buildSubmitButton() {
    return Container(
      height: 64,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        gradient: const LinearGradient(
          colors: [AppTheme.primary, AppTheme.secondary],
        ),
        boxShadow: [
          BoxShadow(
            color: AppTheme.primary.withOpacity(0.3),
            blurRadius: 15,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: ElevatedButton(
        onPressed: _isLoading ? null : _submit,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.transparent,
          foregroundColor: Colors.white,
          shadowColor: Colors.transparent,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
          ),
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
                children: const [
                  Text(
                    'ส่งใบขอลา',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900),
                  ),
                  SizedBox(width: 12),
                  Icon(Icons.send_rounded, size: 20),
                ],
              ),
      ),
    );
  }
}
