import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../providers/guard_change_provider.dart';
import '../services/api_service.dart';
import 'package:intl/intl.dart';
import 'package:google_fonts/google_fonts.dart';
import 'guard_change_success_screen.dart';
import '../widgets/animated_background.dart';

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
  final TextEditingController _searchController = TextEditingController();

  List<dynamic> _users = [];
  List<dynamic> _filteredUsers = []; // For search functionality
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
      duration: const Duration(milliseconds: 800),
    )..forward();
    _fetchUsers();
    _searchController.addListener(_filterUsers);
  }

  @override
  void dispose() {
    _animationController.dispose();
    _remarksController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  void _filterUsers() {
    final query = _searchController.text.toLowerCase();
    setState(() {
      _filteredUsers = _users.where((user) {
        final name = (user['name'] ?? '').toString().toLowerCase();
        final rank = (user['rank'] ?? '').toString().toLowerCase();
        return name.contains(query) || rank.contains(query);
      }).toList();
    });
  }

  Future<void> _fetchUsers() async {
    try {
      final response = await _apiService.getGuardChangeUsers();
      if (response.data['success']) {
        final List<dynamic> allUsers = response.data['data'];

        final filteredByDept = allUsers.where((user) {
          final dept = (user['department'] ?? '').toString().toLowerCase();

          // Check for Governance / Administration (General) / Department of Provincial Administration?
          // Using keywords from DirectoryProvider to be safe
          if (dept.contains('ปกครอง') || dept.contains('govern')) return true;

          // Education
          if (dept.contains('ศึกษา') ||
              dept.contains('edu') ||
              dept.contains('study') ||
              dept.contains('train'))
            return true;

          // Support
          if (dept.contains('สนับสนุน') ||
              dept.contains('support') ||
              dept.contains('supply'))
            return true;

          // Admin (Clerk/Office)
          if (dept.contains('ธุรการ') || dept.contains('admin')) return true;

          // Finance
          if (dept.contains('การเงิน') ||
              dept.contains('fin') ||
              dept.contains('account'))
            return true;

          return false;
        }).toList();

        setState(() {
          _users = filteredByDept;
          _filteredUsers = _users;
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

    // Using AppTheme colors directly for consistency with the design request
    const kBlueAccent = AppTheme.primary; // Use AppTheme primary
    const kBlueDark = AppTheme.textMain; // Use AppTheme textMain

    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: Text(
          'ขอเปลี่ยนเวรยาม',
          style: GoogleFonts.kanit(
            fontWeight: FontWeight.w700,
            fontSize: 22,
            color: kBlueDark,
          ),
        ),
        centerTitle: false,
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: Container(
          margin: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.5),
            shape: BoxShape.circle,
          ),
          child: IconButton(
            icon: const Icon(
              Icons.arrow_back_ios_new_rounded,
              size: 20,
              color: kBlueDark,
            ),
            onPressed: () => Navigator.pop(context),
          ),
        ),
      ),
      body: Stack(
        children: [
          const Positioned.fill(child: AnimatedBackground()),
          SafeArea(
            child: _isLoadingUsers
                ? const Center(child: CircularProgressIndicator())
                : Column(
                    children: [
                      Expanded(
                        child: FadeTransition(
                          opacity: _animationController,
                          child: SingleChildScrollView(
                            physics: const BouncingScrollPhysics(),
                            padding: const EdgeInsets.all(20),
                            child: Form(
                              key: _formKey,
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  // SECTION 1: SELECT YOUR SHIFT (Card Style)
                                  Text(
                                    'ข้อมูลเวรยาม',
                                    style: GoogleFonts.kanit(
                                      fontSize: 14,
                                      fontWeight: FontWeight.w600,
                                      color: Colors.grey[600],
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                  const SizedBox(height: 12),
                                  _buildShiftSelectionCard(kBlueAccent),

                                  const SizedBox(height: 24),

                                  // SECTION 2: SELECT COLLEAGUE
                                  Text(
                                    'เลือกผู้มาเปลี่ยนแทน',
                                    style: GoogleFonts.kanit(
                                      fontSize: 14,
                                      fontWeight: FontWeight.w600,
                                      color: Colors.grey[600],
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                  const SizedBox(height: 12),
                                  _buildSearchField(),
                                  const SizedBox(height: 12),
                                  _buildColleagueList(kBlueAccent),

                                  const SizedBox(height: 24),

                                  // SECTION 3: REASON
                                  Text(
                                    'เหตุผลการขอเปลี่ยน',
                                    style: GoogleFonts.kanit(
                                      fontSize: 14,
                                      fontWeight: FontWeight.w600,
                                      color: Colors.grey[600],
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                  const SizedBox(height: 12),
                                  _buildReasonField(),
                                  const SizedBox(
                                    height: 80,
                                  ), // Space for bottom button
                                ],
                              ),
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
          ),
        ],
      ),
      bottomSheet: Container(
        color: Colors.white,
        padding: const EdgeInsets.all(20),
        child: _buildSubmitButton(provider, kBlueAccent),
      ),
    );
  }

  String _formatThaiDate(DateTime date) {
    final thaiMonths = [
      'ม.ค.',
      'ก.พ.',
      'มี.ค.',
      'เม.ย.',
      'พ.ค.',
      'มิ.ย.',
      'ก.ค.',
      'ส.ค.',
      'ก.ย.',
      'ต.ค.',
      'พ.ย.',
      'ธ.ค.',
    ];
    return '${date.day} ${thaiMonths[date.month - 1]} ${date.year + 543}';
  }

  Widget _buildShiftSelectionCard(Color accentColor) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: IntrinsicHeight(
        child: Row(
          children: [
            // Left Blue Accent Line
            Container(
              width: 6,
              decoration: BoxDecoration(
                color: accentColor,
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(16),
                  bottomLeft: Radius.circular(16),
                ),
              ),
            ),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'เลือกวัน และหน้าที่',
                          style: TextStyle(
                            color: accentColor,
                            fontWeight: FontWeight.bold,
                            fontSize: 12,
                            letterSpacing: 1,
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: const Color(0xFFEFF6FF),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Icon(
                            Icons.access_time_filled_rounded,
                            color: accentColor,
                            size: 20,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),

                    // Date Picker Integration
                    InkWell(
                      onTap: _pickDate,
                      child: Row(
                        children: [
                          Text(
                            _selectedDate == null
                                ? 'เลือกวันที่'
                                : _formatThaiDate(_selectedDate!),
                            style: GoogleFonts.kanit(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                              color: const Color(0xFF1E293B),
                            ),
                          ),
                          if (_selectedDate != null)
                            const Padding(
                              padding: EdgeInsets.only(left: 8.0),
                              child: Text(
                                '• 08:00 - 16:00', // Hardcoded time as per example, or generic
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                  color: Color(0xFF1E293B),
                                ),
                              ),
                            ),
                          const SizedBox(width: 8),
                          Icon(
                            Icons.keyboard_arrow_down_rounded,
                            color: Colors.grey[400],
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 8),

                    // Position Dropdown Integration
                    Theme(
                      data: Theme.of(
                        context,
                      ).copyWith(canvasColor: Colors.white),
                      child: DropdownButtonFormField<String>(
                        initialValue: _selectedPosition,
                        decoration: const InputDecoration(
                          isDense: true,
                          contentPadding: EdgeInsets.zero,
                          border: InputBorder.none,
                          enabledBorder: InputBorder.none,
                          focusedBorder: InputBorder.none,
                          errorBorder: InputBorder.none,
                          disabledBorder: InputBorder.none,
                          filled: false,
                          prefixIcon: Icon(
                            Icons.location_on,
                            size: 18,
                            color: Color(0xFF64748B),
                          ),
                          prefixIconConstraints: BoxConstraints(
                            minWidth: 24,
                            minHeight: 0,
                          ),
                        ),
                        hint: Text(
                          'เลือกตำแหน่ง',
                          style: GoogleFonts.kanit(
                            fontSize: 14,
                            color: const Color(0xFF64748B),
                          ),
                        ),
                        style: GoogleFonts.kanit(
                          fontSize: 14,
                          color: const Color(0xFF64748B),
                          fontWeight: FontWeight.w500,
                        ),
                        icon: Icon(
                          Icons.keyboard_arrow_down,
                          size: 16,
                          color: Colors.grey[400],
                        ),
                        items: _positions.entries.map((e) {
                          return DropdownMenuItem(
                            value: e.key,
                            child: Text(e.value),
                          );
                        }).toList(),
                        onChanged: (val) {
                          setState(() => _selectedPosition = val);
                        },
                      ),
                    ),
                    const SizedBox(height: 16),
                    const Divider(height: 1, color: Color(0xFFE2E8F0)),
                    const SizedBox(height: 16),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          children: [
                            CircleAvatar(
                              radius: 14,
                              backgroundColor: Colors.grey[200],
                              child: const Icon(
                                Icons.person,
                                size: 16,
                                color: Colors.grey,
                              ),
                            ),
                            const SizedBox(width: 4),
                            CircleAvatar(
                              radius: 14,
                              backgroundColor: const Color(0xFFDBEAFE),
                              child: Text(
                                "You",
                                style: GoogleFonts.kanit(
                                  fontSize: 10,
                                  color: accentColor,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                          ],
                        ),
                        Text(
                          'REF: #SHIFT-${DateTime.now().millisecondsSinceEpoch.toString().substring(8)}',
                          style: GoogleFonts.roboto(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF94A3B8),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSearchField() {
    return TextField(
      controller: _searchController,
      decoration: InputDecoration(
        prefixIcon: const Icon(Icons.search, color: Color(0xFF94A3B8)),
        hintText: 'ค้นหาชื่อ หรือ ยศ...',
        hintStyle: GoogleFonts.kanit(color: const Color(0xFF94A3B8)),
        contentPadding: const EdgeInsets.symmetric(
          horizontal: 20,
          vertical: 16,
        ),
      ),
    );
  }

  Widget _buildColleagueList(Color accentColor) {
    if (_filteredUsers.isEmpty) {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
        ),
        child: Column(
          children: [
            Icon(
              Icons.person_search_outlined,
              size: 48,
              color: Colors.grey[300],
            ),
            const SizedBox(height: 8),
            Text(
              'ไม่พบรายชื่อ',
              style: GoogleFonts.kanit(color: Colors.grey[400]),
            ),
          ],
        ),
      );
    }

    return ListView.separated(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: _filteredUsers.length,
      separatorBuilder: (_, __) => const SizedBox(height: 12),
      itemBuilder: (context, index) {
        final user = _filteredUsers[index];
        final isSelected = _selectedReplacementId == user['id'];
        final rank = user['rank'] ?? '';
        final name = user['name'] ?? '';
        // Mocking available status since we don't have it in the API response yet
        // In a real app, you'd check scheduling
        final bool isAvailable = true;

        return GestureDetector(
          onTap: () {
            HapticFeedback.selectionClick();
            setState(() => _selectedReplacementId = user['id']);
          },
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 200),
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(
                color: isSelected ? accentColor : Colors.transparent,
                width: 2,
              ),
              boxShadow: isSelected
                  ? [
                      BoxShadow(
                        color: accentColor.withOpacity(0.1),
                        blurRadius: 8,
                        offset: const Offset(0, 4),
                      ),
                    ]
                  : [],
            ),
            child: Row(
              children: [
                CircleAvatar(
                  radius: 24,
                  backgroundColor: isSelected
                      ? accentColor.withOpacity(0.1)
                      : Colors.grey[100],
                  backgroundImage: user['avatar_url'] != null
                      ? NetworkImage(user['avatar_url'])
                      : null,
                  child: user['avatar_url'] == null
                      ? Text(
                          name.isNotEmpty ? name[0] : '?',
                          style: GoogleFonts.kanit(
                            color: isSelected ? accentColor : Colors.grey,
                          ),
                        )
                      : null,
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        '$rank $name',
                        style: GoogleFonts.kanit(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                          color: const Color(0xFF1E293B),
                        ),
                      ),
                      Text(
                        isAvailable ? 'พร้อมปฏิบัติงาน' : 'ติดภารกิจ',
                        style: GoogleFonts.kanit(
                          fontSize: 13,
                          color: isAvailable
                              ? Colors.grey[600]
                              : Colors.red[300],
                        ),
                      ),
                    ],
                  ),
                ),
                if (isSelected)
                  Container(
                    width: 24,
                    height: 24,
                    decoration: BoxDecoration(
                      color: accentColor,
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.check,
                      size: 16,
                      color: Colors.white,
                    ),
                  )
                else
                  Container(
                    width: 24,
                    height: 24,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      border: Border.all(color: Colors.grey[300]!),
                      shape: BoxShape.circle,
                    ),
                  ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildReasonField() {
    return TextFormField(
      controller: _remarksController,
      maxLines: 4,
      style: GoogleFonts.kanit(),
      decoration: InputDecoration(
        hintText: 'อธิบายเหตุผลที่ต้องการขอเปลี่ยน (ไม่บังคับ)...',
        hintStyle: GoogleFonts.kanit(color: const Color(0xFF94A3B8)),
        contentPadding: const EdgeInsets.all(20),
      ),
    );
  }

  Widget _buildSubmitButton(GuardChangeProvider provider, Color accentColor) {
    return Container(
      width: double.infinity,
      height: 56,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        gradient: LinearGradient(
          colors: [accentColor, const Color(0xFF2563EB)],
        ),
        boxShadow: [
          BoxShadow(
            color: accentColor.withOpacity(0.3),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ElevatedButton(
        onPressed: provider.isLoading ? null : _submit,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.transparent,
          shadowColor: Colors.transparent,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
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
                children: [
                  Text(
                    'ส่งคำขอเปลี่ยนยาม',
                    style: GoogleFonts.kanit(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(width: 8),
                  const Icon(Icons.send_rounded, color: Colors.white, size: 20),
                ],
              ),
      ),
    );
  }

  Future<void> _pickDate() async {
    final date = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (context, child) {
        return Theme(
          data: AppTheme.lightTheme.copyWith(
            colorScheme: const ColorScheme.light(primary: Color(0xFF3B82F6)),
          ),
          child: child!,
        );
      },
    );
    if (date != null) setState(() => _selectedDate = date);
  }

  Future<void> _submit() async {
    if (_selectedPosition == null) {
      _showSnackBar('กรุณาเลือกตำแหน่ง', AppTheme.error);
      return;
    }
    if (_selectedDate == null) {
      _showSnackBar('กรุณาเลือกวันที่', AppTheme.error);
      return;
    }
    if (_selectedReplacementId == null) {
      _showSnackBar('กรุณาเลือกผู้มาเปลี่ยนแทน', AppTheme.error);
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
      // Find replacement user details
      final user = _users.firstWhere(
        (u) => u['id'] == _selectedReplacementId,
        orElse: () => {'name': 'Unknown', 'rank': ''},
      );
      final replacementName = '${user['rank'] ?? ''} ${user['name'] ?? ''}';

      // Get position name
      final positionName = _positions[_selectedPosition] ?? _selectedPosition!;

      Navigator.of(context).pushReplacement(
        MaterialPageRoute(
          builder: (context) => GuardChangeSuccessScreen(
            dutyDate: _selectedDate!,
            position: positionName,
            replacementName: replacementName,
          ),
        ),
      );
    } else if (mounted) {
      _showSnackBar('เกิดข้อผิดพลาด', AppTheme.error);
    }
  }

  void _showSnackBar(String message, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            Icon(
              color == AppTheme.success ? Icons.check_circle : Icons.error,
              color: Colors.white,
            ),
            const SizedBox(width: 12),
            Text(message, style: GoogleFonts.kanit()),
          ],
        ),
        backgroundColor: color,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        margin: const EdgeInsets.all(20),
      ),
    );
  }
}
