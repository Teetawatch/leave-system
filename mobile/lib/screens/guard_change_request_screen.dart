import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../providers/guard_change_provider.dart';
import '../services/api_service.dart';
import 'package:intl/intl.dart';

class GuardChangeRequestScreen extends StatefulWidget {
  const GuardChangeRequestScreen({super.key});

  @override
  State<GuardChangeRequestScreen> createState() =>
      _GuardChangeRequestScreenState();
}

class _GuardChangeRequestScreenState extends State<GuardChangeRequestScreen>
    with SingleTickerProviderStateMixin {
  final _formKey = GlobalKey<FormState>();
  final ApiService _apiService = ApiService();
  late AnimationController _animationController;

  String? _selectedPosition;
  int? _selectedReplacementId;
  DateTime? _selectedDate;
  final TextEditingController _remarksController = TextEditingController();

  List<dynamic> _users = [];
  bool _isLoadingUsers = true;

  final Map<String, String> _positions = {
    'senior_duty_officer': 'นายทหารเวรอาวุโส',
    'duty_officer': 'นายทหารเวร',
    'assistant_duty_officer': 'ผู้ช่วยนายทหารเวร',
  };

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1000),
    )..forward();
    _fetchUsers();
  }

  @override
  void dispose() {
    _animationController.dispose();
    _remarksController.dispose();
    super.dispose();
  }

  Future<void> _fetchUsers() async {
    try {
      final response = await _apiService.getGuardChangeUsers();
      if (response.data['success']) {
        setState(() {
          _users = response.data['data'];
          _isLoadingUsers = false;
        });
      }
    } catch (e) {
      debugPrint('Error fetching users: $e');
      setState(() => _isLoadingUsers = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<GuardChangeProvider>(context);
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: Colors.white,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text(
          'ขอเปลี่ยนเวรยาม',
          style: TextStyle(
            fontWeight: FontWeight.w900,
            color: AppTheme.textMain,
          ),
        ),
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(
            Icons.arrow_back_ios_new_rounded,
            color: AppTheme.textMain,
          ),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Stack(
        children: [
          // Background Design
          Container(
            height: size.height,
            width: size.width,
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [
                  Color(0xFFF8FAFC),
                  Colors.white,
                  Color(0xFFFFF7ED),
                ], // Orange tint
              ),
            ),
          ),
          _buildFloatingCircle(
            top: -50,
            right: -50,
            size: 200,
            color: Colors.orange.withOpacity(0.06),
          ),
          _buildFloatingCircle(
            bottom: 100,
            left: -60,
            size: 280,
            color: AppTheme.primary.withOpacity(0.04),
          ),

          SafeArea(
            child: _isLoadingUsers
                ? const Center(child: CircularProgressIndicator())
                : FadeTransition(
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

                            // Main Card
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
                                  filter: ImageFilter.blur(
                                    sigmaX: 5,
                                    sigmaY: 5,
                                  ),
                                  child: Padding(
                                    padding: const EdgeInsets.all(28),
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        _buildSectionLabel(
                                          'ตำแหน่งเวรยาม',
                                          Icons.security_rounded,
                                        ),
                                        const SizedBox(height: 12),
                                        _buildPositionSelector(),
                                        const SizedBox(height: 24),

                                        _buildSectionLabel(
                                          'วันที่ปฏิบัติเวร',
                                          Icons.calendar_month_rounded,
                                        ),
                                        const SizedBox(height: 12),
                                        _buildDatePicker(),
                                        const SizedBox(height: 24),

                                        _buildSectionLabel(
                                          'ผู้มาเปลี่ยนแทน',
                                          Icons.person_search_rounded,
                                        ),
                                        const SizedBox(height: 12),
                                        _buildReplacementSelector(),
                                        const SizedBox(height: 24),

                                        _buildSectionLabel(
                                          'หมายเหตุเพิ่มเติม',
                                          Icons.notes_rounded,
                                        ),
                                        const SizedBox(height: 12),
                                        _buildRemarksField(),
                                      ],
                                    ),
                                  ),
                                ),
                              ),
                            ),

                            const SizedBox(height: 40),
                            _buildSubmitButton(provider),
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
          'ระบุรายละเอียดการเปลี่ยนเวร',
          style: TextStyle(
            fontSize: 26,
            fontWeight: FontWeight.w900,
            color: AppTheme.textMain,
            letterSpacing: -0.5,
          ),
        ),
        const SizedBox(height: 8),
        Text(
          'กรุณาตรวจสอบผู้มาเปลี่ยนแทนว่าสามารถปฏิบัติภารกิจได้',
          style: TextStyle(
            color: AppTheme.textSub.withOpacity(0.7),
            fontSize: 13,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }

  Widget _buildSectionLabel(String text, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 18, color: Colors.orange),
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

  Widget _buildPositionSelector() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      decoration: BoxDecoration(
        color: Colors.orange.withOpacity(0.08),
        borderRadius: BorderRadius.circular(20),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButtonFormField<String>(
          decoration: const InputDecoration(
            border: InputBorder.none,
            filled: false,
          ),
          value: _selectedPosition,
          items: _positions.entries
              .map(
                (e) => DropdownMenuItem(
                  value: e.key,
                  child: Text(
                    e.value,
                    style: const TextStyle(fontWeight: FontWeight.w700),
                  ),
                ),
              )
              .toList(),
          onChanged: (val) => setState(() => _selectedPosition = val),
          validator: (val) => val == null ? 'กรุณาเลือกตำแหน่ง' : null,
          hint: const Text(
            'เลือกตำแหน่ง',
            style: TextStyle(fontWeight: FontWeight.w500, fontSize: 14),
          ),
        ),
      ),
    );
  }

  Widget _buildDatePicker() {
    return InkWell(
      onTap: () async {
        final date = await showDatePicker(
          context: context,
          initialDate: DateTime.now(),
          firstDate: DateTime.now(),
          lastDate: DateTime.now().add(const Duration(days: 365)),
        );
        if (date != null) setState(() => _selectedDate = date);
      },
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.orange.withOpacity(0.08),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              _selectedDate == null
                  ? 'เลือกวันที่'
                  : DateFormat('dd MMM yyyy').format(_selectedDate!),
              style: TextStyle(
                color: _selectedDate == null
                    ? AppTheme.textSub.withOpacity(0.5)
                    : AppTheme.textMain,
                fontSize: 14,
                fontWeight: FontWeight.w800,
              ),
            ),
            const Icon(Icons.keyboard_arrow_down_rounded, color: Colors.orange),
          ],
        ),
      ),
    );
  }

  Widget _buildReplacementSelector() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      decoration: BoxDecoration(
        color: Colors.orange.withOpacity(0.08),
        borderRadius: BorderRadius.circular(20),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButtonFormField<int>(
          decoration: const InputDecoration(
            border: InputBorder.none,
            filled: false,
          ),
          value: _selectedReplacementId,
          items: _users
              .map(
                (u) => DropdownMenuItem<int>(
                  value: u['id'],
                  child: Text(
                    '${u['rank']}${u['name']}',
                    style: const TextStyle(fontWeight: FontWeight.w700),
                  ),
                ),
              )
              .toList(),
          onChanged: (val) => setState(() => _selectedReplacementId = val),
          validator: (val) => val == null ? 'กรุณาเลือกผู้มาเปลี่ยนแทน' : null,
          hint: const Text(
            'ค้นหาเจ้าหน้าที่',
            style: TextStyle(fontWeight: FontWeight.w500, fontSize: 14),
          ),
        ),
      ),
    );
  }

  Widget _buildRemarksField() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.orange.withOpacity(0.08),
        borderRadius: BorderRadius.circular(20),
      ),
      child: TextFormField(
        controller: _remarksController,
        maxLines: 3,
        style: const TextStyle(
          fontWeight: FontWeight.w600,
          color: AppTheme.textMain,
          fontSize: 14,
        ),
        decoration: InputDecoration(
          hintText: 'เหตุผลการขอเปลี่ยน หรือข้อความเพิ่มเติม...',
          hintStyle: TextStyle(
            color: AppTheme.textSub.withOpacity(0.5),
            fontSize: 14,
          ),
          border: InputBorder.none,
          contentPadding: const EdgeInsets.all(16),
        ),
      ),
    );
  }

  Widget _buildSubmitButton(GuardChangeProvider provider) {
    return Container(
      height: 64,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        gradient: const LinearGradient(
          colors: [Colors.orange, Colors.deepOrange],
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.orange.withOpacity(0.3),
            blurRadius: 15,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: ElevatedButton(
        onPressed: provider.isLoading ? null : _submit,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.transparent,
          foregroundColor: Colors.white,
          shadowColor: Colors.transparent,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
          ),
        ),
        child: provider.isLoading
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
                    'ส่งคำขอเปลี่ยนยาม',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900),
                  ),
                  SizedBox(width: 12),
                  Icon(Icons.swap_horiz_rounded, size: 24),
                ],
              ),
      ),
    );
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedDate == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('กรุณาเลือกวันที่'),
          behavior: SnackBarBehavior.floating,
        ),
      );
      return;
    }

    final provider = Provider.of<GuardChangeProvider>(context, listen: false);
    final success = await provider.submitRequest({
      'duty_position': _selectedPosition,
      'replacement_user_id': _selectedReplacementId,
      'duty_date': DateFormat('yyyy-MM-dd').format(_selectedDate!),
      'remarks': _remarksController.text,
    });

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text(
            'ส่งคำขอเปลี่ยนยามเรียบร้อยแล้ว',
            style: TextStyle(fontWeight: FontWeight.w600),
          ),
          backgroundColor: AppTheme.success,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
        ),
      );
      Navigator.pop(context);
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text('เกิดข้อผิดพลาดในการส่งคำขอ'),
          backgroundColor: AppTheme.error,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
        ),
      );
    }
  }
}
