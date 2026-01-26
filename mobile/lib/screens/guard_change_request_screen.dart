import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../providers/guard_change_provider.dart';
import '../services/api_service.dart';
import '../widgets/animated_background.dart';
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

    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text(
          'ขอเปลี่ยนเวรยาม',
          style: TextStyle(
            fontWeight: FontWeight.w900,
            color: Color(0xFF1E293B),
            letterSpacing: -0.5,
          ),
        ),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        systemOverlayStyle: SystemUiOverlayStyle.dark,
        leading: IconButton(
          onPressed: () => Navigator.pop(context),
          icon: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.5),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.arrow_back_ios_new_rounded,
              size: 20,
              color: Color(0xFF1E293B),
            ),
          ),
        ),
      ),
      body: Stack(
        children: [
          const Positioned.fill(child: AnimatedBackground()),

          SafeArea(
            child: _isLoadingUsers
                ? const Center(child: CircularProgressIndicator())
                : FadeTransition(
                    opacity: _animationController,
                    child: SingleChildScrollView(
                      physics: const BouncingScrollPhysics(),
                      padding: const EdgeInsets.symmetric(
                        horizontal: 24,
                        vertical: 20,
                      ),
                      child: Form(
                        key: _formKey,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            _buildHeaderSection(),
                            const SizedBox(height: 24),

                            Container(
                              decoration: BoxDecoration(
                                color: Colors.white.withOpacity(0.8),
                                borderRadius: BorderRadius.circular(32),
                                boxShadow: [
                                  BoxShadow(
                                    color: const Color(
                                      0xFF6366F1,
                                    ).withOpacity(0.1),
                                    blurRadius: 30,
                                    offset: const Offset(0, 10),
                                  ),
                                ],
                                border: Border.all(color: Colors.white),
                              ),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(32),
                                child: BackdropFilter(
                                  filter: ImageFilter.blur(
                                    sigmaX: 10,
                                    sigmaY: 10,
                                  ),
                                  child: Padding(
                                    padding: const EdgeInsets.all(24),
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

                            const SizedBox(height: 32),
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

  Widget _buildHeaderSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'ระบุรายละเอียด',
          style: TextStyle(
            fontSize: 28,
            fontWeight: FontWeight.w900,
            color: Color(0xFF1E293B),
            letterSpacing: -1,
            height: 1.2,
          ),
        ),
        const SizedBox(height: 8),
        Text(
          'กรุณาตรวจสอบผู้มาเปลี่ยนแทนว่าสามารถปฏิบัติภารกิจได้',
          style: TextStyle(
            color: const Color(0xFF64748B).withOpacity(0.8),
            fontSize: 14,
            fontWeight: FontWeight.w500,
            height: 1.5,
          ),
        ),
      ],
    );
  }

  Widget _buildSectionLabel(String text, IconData icon) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: const Color(0xFF6366F1).withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, size: 18, color: const Color(0xFF6366F1)),
        ),
        const SizedBox(width: 12),
        Text(
          text,
          style: const TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.w800,
            color: Color(0xFF1E293B),
          ),
        ),
      ],
    );
  }

  Widget _buildPositionSelector() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButtonFormField<String>(
          decoration: const InputDecoration(
            border: InputBorder.none,
            filled: false,
            contentPadding: EdgeInsets.zero,
          ),
          icon: const Icon(
            Icons.keyboard_arrow_down_rounded,
            color: Color(0xFF64748B),
          ),
          value: _selectedPosition,
          items: _positions.entries
              .map(
                (e) => DropdownMenuItem(
                  value: e.key,
                  child: Text(
                    e.value,
                    style: const TextStyle(
                      fontWeight: FontWeight.w700,
                      color: Color(0xFF334155),
                    ),
                  ),
                ),
              )
              .toList(),
          onChanged: (val) {
            HapticFeedback.lightImpact();
            setState(() => _selectedPosition = val);
          },
          validator: (val) => val == null ? 'กรุณาเลือกตำแหน่ง' : null,
          hint: const Text(
            'เลือกตำแหน่ง',
            style: TextStyle(
              fontWeight: FontWeight.w500,
              fontSize: 14,
              color: Color(0xFF94A3B8),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildDatePicker() {
    return GestureDetector(
      onTap: () async {
        HapticFeedback.lightImpact();
        final date = await showDatePicker(
          context: context,
          initialDate: DateTime.now(),
          firstDate: DateTime.now(),
          lastDate: DateTime.now().add(const Duration(days: 365)),
          builder: (context, child) {
            return Theme(
              data: Theme.of(context).copyWith(
                colorScheme: const ColorScheme.light(
                  primary: Color(0xFF6366F1),
                  onPrimary: Colors.white,
                  onSurface: Color(0xFF1E293B),
                ),
                textButtonTheme: TextButtonThemeData(
                  style: TextButton.styleFrom(
                    foregroundColor: const Color(0xFF6366F1),
                    textStyle: const TextStyle(fontWeight: FontWeight.w700),
                  ),
                ),
              ),
              child: child!,
            );
          },
        );
        if (date != null) setState(() => _selectedDate = date);
      },
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: _selectedDate != null
              ? const Color(0xFF6366F1).withOpacity(0.05)
              : const Color(0xFFF8FAFC),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: _selectedDate != null
                ? const Color(0xFF6366F1).withOpacity(0.3)
                : const Color(0xFFE2E8F0),
          ),
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
                    ? const Color(0xFF94A3B8)
                    : const Color(0xFF1E293B),
                fontSize: 14,
                fontWeight: FontWeight.w800,
              ),
            ),
            Icon(
              Icons.calendar_today_rounded,
              size: 20,
              color: _selectedDate != null
                  ? const Color(0xFF6366F1)
                  : const Color(0xFF94A3B8),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildReplacementSelector() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButtonFormField<int>(
          decoration: const InputDecoration(
            border: InputBorder.none,
            filled: false,
            contentPadding: EdgeInsets.zero,
          ),
          icon: const Icon(Icons.search_rounded, color: Color(0xFF64748B)),
          value: _selectedReplacementId,
          items: _users
              .map(
                (u) => DropdownMenuItem<int>(
                  value: u['id'],
                  child: Text(
                    '${u['rank']}${u['name']}',
                    style: const TextStyle(
                      fontWeight: FontWeight.w700,
                      color: Color(0xFF334155),
                    ),
                  ),
                ),
              )
              .toList(),
          onChanged: (val) {
            HapticFeedback.lightImpact();
            setState(() => _selectedReplacementId = val);
          },
          validator: (val) => val == null ? 'กรุณาเลือกผู้มาเปลี่ยนแทน' : null,
          hint: const Text(
            'ค้นหาเจ้าหน้าที่',
            style: TextStyle(
              fontWeight: FontWeight.w500,
              fontSize: 14,
              color: Color(0xFF94A3B8),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRemarksField() {
    return Container(
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: TextFormField(
        controller: _remarksController,
        maxLines: 3,
        style: const TextStyle(
          fontWeight: FontWeight.w600,
          color: Color(0xFF334155),
          fontSize: 14,
        ),
        decoration: const InputDecoration(
          hintText: 'เหตุผลการขอเปลี่ยน หรือข้อความเพิ่มเติม...',
          hintStyle: TextStyle(
            color: Color(0xFF94A3B8),
            fontSize: 14,
            fontWeight: FontWeight.w500,
          ),
          border: InputBorder.none,
          contentPadding: EdgeInsets.all(16),
        ),
      ),
    );
  }

  Widget _buildSubmitButton(GuardChangeProvider provider) {
    return Container(
      height: 60,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        gradient: const LinearGradient(
          colors: [Color(0xFF6366F1), Color(0xFF8B5CF6)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF6366F1).withOpacity(0.4),
            blurRadius: 20,
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
            : const Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    'ส่งคำขอเปลี่ยนยาม',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w800,
                      letterSpacing: 0.5,
                    ),
                  ),
                  SizedBox(width: 12),
                  Icon(Icons.swap_horiz_rounded, size: 24),
                ],
              ),
      ),
    );
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) {
      HapticFeedback.heavyImpact();
      return;
    }
    if (_selectedDate == null) {
      HapticFeedback.heavyImpact();
      _showSnackBar('กรุณาเลือกวันที่', AppTheme.error);
      return;
    }

    HapticFeedback.mediumImpact();
    final provider = Provider.of<GuardChangeProvider>(context, listen: false);
    final success = await provider.submitRequest({
      'duty_position': _selectedPosition,
      'replacement_user_id': _selectedReplacementId,
      'duty_date': DateFormat('yyyy-MM-dd').format(_selectedDate!),
      'remarks': _remarksController.text,
    });

    if (success && mounted) {
      _showSnackBar('ส่งคำขอเปลี่ยนยามเรียบร้อยแล้ว', AppTheme.success);
      Navigator.pop(context);
    } else if (mounted) {
      _showSnackBar('เกิดข้อผิดพลาดในการส่งคำขอ', AppTheme.error);
    }
  }

  void _showSnackBar(String message, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            Icon(
              color == AppTheme.success
                  ? Icons.check_circle_rounded
                  : Icons.error_rounded,
              color: Colors.white,
            ),
            const SizedBox(width: 12),
            Text(
              message,
              style: const TextStyle(
                fontWeight: FontWeight.w700,
                fontFamily: 'NotoSansThai',
              ),
            ),
          ],
        ),
        backgroundColor: color,
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.all(24),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        elevation: 4,
      ),
    );
  }
}
