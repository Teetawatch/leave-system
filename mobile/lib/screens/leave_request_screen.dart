import 'dart:ui'; // Required for PathMetric
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../models/leave_type_model.dart';
import '../providers/leave_provider.dart';
import 'leave_success_screen.dart';

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
  final TextEditingController _contactAddressController =
      TextEditingController();

  // Sick Leave specific fields
  final TextEditingController _houseNoController = TextEditingController();
  final TextEditingController _streetController =
      TextEditingController(); // Represents Soi/Road
  String? _selectedProvince;

  static const List<String> _provinces = [
    'กรุงเทพมหานคร',
    'กระบี่',
    'กาญจนบุรี',
    'กาฬสินธุ์',
    'กำแพงเพชร',
    'ขอนแก่น',
    'จันทบุรี',
    'ฉะเชิงเทรา',
    'ชลบุรี',
    'ชัยนาท',
    'ชัยภูมิ',
    'ชุมพร',
    'เชียงราย',
    'เชียงใหม่',
    'ตรัง',
    'ตราด',
    'ตาก',
    'นครนายก',
    'นครปฐม',
    'นครพนม',
    'นครราชสีมา',
    'นครศรีธรรมราช',
    'นครสวรรค์',
    'นนทบุรี',
    'นราธิวาส',
    'น่าน',
    'บึงกาฬ',
    'บุรีรัมย์',
    'ปทุมธานี',
    'ประจวบคีรีขันธ์',
    'ปราจีนบุรี',
    'ปัตตานี',
    'พระนครศรีอยุธยา',
    'พะเยา',
    'พังงา',
    'พัทลุง',
    'พิจิตร',
    'พิษณุโลก',
    'เพชรบุรี',
    'เพชรบูรณ์',
    'แพร่',
    'ภูเก็ต',
    'มหาสารคาม',
    'มุกดาหาร',
    'แม่ฮ่องสอน',
    'ยโสธร',
    'ยะลา',
    'ร้อยเอ็ด',
    'ระนอง',
    'ระยอง',
    'ราชบุรี',
    'ลพบุรี',
    'ลำปาง',
    'ลำพูน',
    'เลย',
    'ศรีสะเกษ',
    'สกลนคร',
    'สงขลา',
    'สตูล',
    'สมุทรปราการ',
    'สมุทรสงคราม',
    'สมุทรสาคร',
    'สระแก้ว',
    'สระบุรี',
    'สิงห์บุรี',
    'สุโขทัย',
    'สุพรรณบุรี',
    'สุราษฎร์ธานี',
    'สุรินทร์',
    'หนองคาย',
    'หนองบัวลำภู',
    'อ่างทอง',
    'อำนาจเจริญ',
    'อุดรธานี',
    'อุตรดิตถ์',
    'อุทัยธานี',
    'อุบลราชธานี',
  ];

  // File upload
  XFile? _selectedFile;
  final ImagePicker _picker = ImagePicker();

  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<LeaveProvider>(context, listen: false).fetchLeaveTypes();
    });
  }

  @override
  void dispose() {
    _reasonController.dispose();
    _contactAddressController.dispose();
    _houseNoController.dispose();
    _streetController.dispose();
    super.dispose();
  }

  Future<void> _pickFile() async {
    try {
      final XFile? file = await _picker.pickImage(
        source: ImageSource.gallery,
        imageQuality: 50,
      );
      if (file != null) {
        setState(() => _selectedFile = file);
      }
    } catch (e) {
      debugPrint('Error picking file: $e');
    }
  }

  Future<void> _selectDate(bool isStart) async {
    HapticFeedback.lightImpact();
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
              primary: Color(0xFF6366F1),
              onPrimary: Colors.white,
              onSurface: Color(0xFF1E293B),
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
    if (_startDate == null || (_endDate == null && !_isTemporaryLeave)) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('กรุณาเลือกวันที่ต้องการลา')),
      );
      return;
    }

    setState(() => _isLoading = true);
    FocusScope.of(context).unfocus();

    List<String> contactAddress = [];
    if (_useDetailedAddress) {
      contactAddress = [
        _houseNoController.text,
        _streetController.text,
        _selectedProvince ?? '',
      ];
    } else if (_contactAddressController.text.isNotEmpty) {
      contactAddress = [_contactAddressController.text];
    } else {
      contactAddress = ['-'];
    }

    // Prepare data
    DateTime start = _startDate!;
    DateTime end = _isTemporaryLeave ? _startDate! : _endDate!;
    String reason = _reasonController.text;

    if (_isTemporaryLeave && _temporarySlot != null) {
      reason = '$reason ($_temporarySlot)';
    }

    final provider = Provider.of<LeaveProvider>(context, listen: false);

    final data = {
      'leave_type_id': _selectedType!.id,
      'start_date': DateFormat('yyyy-MM-dd').format(start),
      'end_date': DateFormat('yyyy-MM-dd').format(end),
      'reason': reason,
      'contact_address': contactAddress,
    };

    // Todo: Handle file upload if backend supports it. Currently just UI.

    final Map<String, dynamic> response = await provider.submitRequest(data);

    if (mounted) {
      setState(() => _isLoading = false);
      final bool isSuccess = response['success'] == true;
      final String message =
          response['message'] ??
          (isSuccess ? 'ทำรายการสำเร็จ' : 'เกิดข้อผิดพลาด');

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(message),
          backgroundColor: isSuccess ? Colors.green : Colors.red,
          behavior: SnackBarBehavior.floating,
        ),
      );

      if (isSuccess) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (context) => LeaveSuccessScreen(
              leaveType: _selectedType!,
              startDate: start,
              endDate: end,
            ),
          ),
        );
      }
    }
  }

  bool get _isSickLeave {
    if (_selectedType == null) return false;
    final name = _selectedType!.name.toLowerCase();
    final slug = _selectedType!.slug.toLowerCase();
    return slug.contains('sick') || name.contains('ป่วย');
  }

  bool get _isPersonalLeave {
    if (_selectedType == null) return false;
    final name = _selectedType!.name.toLowerCase();
    final slug = _selectedType!.slug.toLowerCase();
    // Check for Personal Business Leave keywords
    return slug.contains('personal') ||
        slug.contains('business') ||
        name.contains('กิจ');
  }

  bool get _isTemporaryLeave {
    if (_selectedType == null) return false;
    final name = _selectedType!.name.toLowerCase();
    final slug = _selectedType!.slug.toLowerCase();
    return slug.contains('temporary') || name.contains('ชั่วกาล');
  }

  bool get _useDetailedAddress => _isSickLeave || _isPersonalLeave;

  String? _temporarySlot;
  final List<String> _temporarySlots = [
    'ช่วงเช้า (ก่อน 07.30)',
    'ช่วงบ่าย (ก่อน 11.00)',
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA), // Light background
      appBar: AppBar(
        title: Text(
          'ยื่นใบลา',
          style: GoogleFonts.kanit(
            fontSize: 18,
            fontWeight: FontWeight.w700,
            color: Colors.black,
          ),
        ),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(
            Icons.arrow_back_ios_new,
            size: 20,
            color: Colors.black,
          ),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildLabel('ประเภทการลา'),
              const SizedBox(height: 8),
              _buildLeaveTypeDropdown(),
              const SizedBox(height: 24),
              if (_isTemporaryLeave) ...[
                _buildLabel('วันที่ลา'),
                const SizedBox(height: 8),
                _buildDatePicker(
                  _startDate,
                  'เลือกวันที่',
                  () => _selectDate(true),
                ),
                const SizedBox(height: 24),
                _buildLabel('ช่วงเวลา'),
                const SizedBox(height: 8),
                _buildTemporarySlotDropdown(),
              ] else
                Row(
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildLabel('วันที่เริ่มลา'),
                          const SizedBox(height: 8),
                          _buildDatePicker(
                            _startDate,
                            'เลือกวันที่',
                            () => _selectDate(true),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildLabel('ถึงวันที่'),
                          const SizedBox(height: 8),
                          _buildDatePicker(
                            _endDate,
                            'เลือกวันที่',
                            () => _selectDate(false),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              const SizedBox(height: 24),
              _buildLabel('เหตุผล/ความจำเป็น'),
              const SizedBox(height: 8),
              _buildReasonField(),

              if (_selectedType != null) ...[
                const SizedBox(height: 24),
                _buildLabel('ที่อยู่ที่ติดต่อได้ระหว่างลา'),
                const SizedBox(height: 8),
                if (_useDetailedAddress)
                  _buildDetailedAddressFields()
                else
                  _buildContactAddressField(),
              ],

              const SizedBox(height: 24),
              _buildLabel(
                _isSickLeave ? 'ใบรับรองแพทย์ (ถ้ามี)' : 'แนบไฟล์ (ไม่บังคับ)',
              ),
              const SizedBox(height: 8),
              _buildAttachmentArea(),
              const SizedBox(height: 32),
              _buildSubmitButton(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTemporarySlotDropdown() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButtonFormField<String>(
          isExpanded: true,
          value: _temporarySlot,
          icon: const Icon(
            Icons.keyboard_arrow_down_rounded,
            color: Color(0xFF64748B),
          ),
          decoration: const InputDecoration(
            border: InputBorder.none,
            enabledBorder: InputBorder.none,
            focusedBorder: InputBorder.none,
            errorBorder: InputBorder.none,
            disabledBorder: InputBorder.none,
            contentPadding: EdgeInsets.symmetric(vertical: 16),
          ),
          hint: Text(
            'เลือกช่วงเวลา...',
            style: GoogleFonts.sarabun(color: const Color(0xFF94A3B8)),
          ),
          items: _temporarySlots.map((slot) {
            return DropdownMenuItem(
              value: slot,
              child: Text(
                slot,
                style: GoogleFonts.sarabun(
                  fontSize: 14,
                  color: const Color(0xFF334155),
                ),
                overflow: TextOverflow.ellipsis,
              ),
            );
          }).toList(),
          onChanged: (val) => setState(() => _temporarySlot = val),
          validator: (val) {
            if (_isTemporaryLeave && val == null) {
              return 'กรุณาเลือกช่วงเวลา';
            }
            return null;
          },
        ),
      ),
    );
  }

  Widget _buildDetailedAddressFields() {
    return Column(
      children: [
        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              flex: 2,
              child: TextFormField(
                controller: _houseNoController,
                enabled: !_isLoading,
                style: GoogleFonts.sarabun(
                  fontSize: 14,
                  color: const Color(0xFF334155),
                ),
                decoration: InputDecoration(
                  filled: true,
                  fillColor: Colors.white,
                  hintText: 'บ้านเลขที่...',
                  hintStyle: GoogleFonts.sarabun(
                    color: const Color(0xFF94A3B8),
                  ),
                  contentPadding: const EdgeInsets.all(16),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                    borderSide: const BorderSide(
                      color: Color(0xFF6366F1),
                      width: 1.5,
                    ),
                  ),
                ),
                validator: (val) {
                  if (_useDetailedAddress && (val == null || val.isEmpty)) {
                    return 'ระบุ';
                  }
                  return null;
                },
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              flex: 3,
              child: TextFormField(
                controller: _streetController,
                enabled: !_isLoading,
                style: GoogleFonts.sarabun(
                  fontSize: 14,
                  color: const Color(0xFF334155),
                ),
                decoration: InputDecoration(
                  filled: true,
                  fillColor: Colors.white,
                  hintText: 'ถนน / ซอย...',
                  hintStyle: GoogleFonts.sarabun(
                    color: const Color(0xFF94A3B8),
                  ),
                  contentPadding: const EdgeInsets.all(16),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                    borderSide: const BorderSide(
                      color: Color(0xFF6366F1),
                      width: 1.5,
                    ),
                  ),
                ),
                validator: (val) {
                  if (_useDetailedAddress && (val == null || val.isEmpty)) {
                    return 'ระบุถนน/ซอย';
                  }
                  return null;
                },
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: const Color(0xFFE2E8F0)),
          ),
          child: DropdownButtonHideUnderline(
            child: DropdownButtonFormField<String>(
              isExpanded: true,
              value: _selectedProvince,
              icon: const Icon(
                Icons.keyboard_arrow_down_rounded,
                color: Color(0xFF64748B),
              ),
              decoration: const InputDecoration(
                border: InputBorder.none,
                enabledBorder: InputBorder.none,
                focusedBorder: InputBorder.none,
                errorBorder: InputBorder.none,
                disabledBorder: InputBorder.none,
                contentPadding: EdgeInsets.symmetric(vertical: 16),
              ),
              hint: Text(
                'เลือกจังหวัด...',
                style: GoogleFonts.sarabun(color: const Color(0xFF94A3B8)),
              ),
              items: _provinces.map((province) {
                return DropdownMenuItem(
                  value: province,
                  child: Text(
                    province,
                    style: GoogleFonts.sarabun(
                      fontSize: 14,
                      color: const Color(0xFF334155),
                    ),
                    overflow: TextOverflow.ellipsis,
                  ),
                );
              }).toList(),
              onChanged: (val) => setState(() => _selectedProvince = val),
              validator: (val) {
                if (_useDetailedAddress && val == null) {
                  return 'กรุณาเลือกจังหวัด';
                }
                return null;
              },
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildLabel(String text) {
    return Text(
      text,
      style: GoogleFonts.kanit(
        fontSize: 14,
        fontWeight: FontWeight.w500,
        color: const Color(0xFF1E293B),
      ),
    );
  }

  Widget _buildLeaveTypeDropdown() {
    return Consumer<LeaveProvider>(
      builder: (context, provider, child) {
        // Filter out Official Business (name contains 'ราชการ' or slug 'official')
        final types = provider.leaveTypes.where((t) {
          final name = t.name.toLowerCase();
          final slug = t.slug.toLowerCase();
          return !name.contains('ราชการ') && !slug.contains('official');
        }).toList();

        return Container(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: const Color(0xFFE2E8F0)),
          ),
          child: DropdownButtonHideUnderline(
            child: DropdownButtonFormField<LeaveType>(
              isExpanded: true,
              value: _selectedType,
              icon: const Icon(
                Icons.keyboard_arrow_down_rounded,
                color: Color(0xFF64748B),
              ),
              decoration: const InputDecoration(
                border: InputBorder.none,
                enabledBorder: InputBorder.none,
                focusedBorder: InputBorder.none,
                errorBorder: InputBorder.none,
                disabledBorder: InputBorder.none,
                contentPadding: EdgeInsets.symmetric(vertical: 16),
              ),
              hint: Text(
                'เลือกประเภท...',
                style: GoogleFonts.sarabun(color: const Color(0xFF94A3B8)),
              ),
              items: types.map((type) {
                return DropdownMenuItem(
                  value: type,
                  child: Text(
                    type.name,
                    style: GoogleFonts.sarabun(
                      fontSize: 14,
                      color: const Color(0xFF334155),
                    ),
                    overflow: TextOverflow.ellipsis,
                  ),
                );
              }).toList(),
              onChanged: (val) {
                setState(() => _selectedType = val);
              },
              validator: (val) => val == null ? 'กรุณาเลือกประเภทการลา' : null,
            ),
          ),
        );
      },
    );
  }

  Widget _buildDatePicker(DateTime? date, String hint, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: const Color(0xFFE2E8F0)),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              date != null ? DateFormat('dd/MM/yyyy').format(date) : hint,
              style: GoogleFonts.sarabun(
                fontSize: 14,
                color: date != null
                    ? const Color(0xFF334155)
                    : const Color(0xFF94A3B8),
              ),
            ),
            const Icon(
              Icons.calendar_today_rounded,
              size: 18,
              color: Color(0xFF64748B),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildReasonField() {
    return TextFormField(
      controller: _reasonController,
      enabled: !_isLoading,
      maxLines: 4,
      keyboardType: TextInputType.multiline,
      style: GoogleFonts.sarabun(fontSize: 14, color: const Color(0xFF334155)),
      decoration: InputDecoration(
        filled: true,
        fillColor: Colors.white,
        hintText: 'ระบุเหตุผลการลา...',
        hintStyle: GoogleFonts.sarabun(color: const Color(0xFF94A3B8)),
        contentPadding: const EdgeInsets.all(16),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFF6366F1), width: 1.5),
        ),
      ),
      validator: (val) => val!.isEmpty ? 'กรุณาระบุเหตุผล' : null,
    );
  }

  Widget _buildContactAddressField() {
    return TextFormField(
      controller: _contactAddressController,
      enabled: !_isLoading,
      maxLines: 2,
      keyboardType: TextInputType.streetAddress,
      style: GoogleFonts.sarabun(fontSize: 14, color: const Color(0xFF334155)),
      decoration: InputDecoration(
        filled: true,
        fillColor: Colors.white,
        hintText: 'ระบุที่อยู่...',
        hintStyle: GoogleFonts.sarabun(color: const Color(0xFF94A3B8)),
        contentPadding: const EdgeInsets.all(16),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFF6366F1), width: 1.5),
        ),
      ),
      validator: (val) {
        if (!_isSickLeave && (val == null || val.isEmpty)) {
          return 'กรุณาระบุที่อยู่';
        }
        return null;
      },
    );
  }

  Widget _buildAttachmentArea() {
    return GestureDetector(
      onTap: _pickFile,
      child: CustomPaint(
        painter: DashedBorderPainter(),
        child: Container(
          width: double.infinity,
          padding: const EdgeInsets.symmetric(vertical: 32),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            // border is handled by painter
          ),
          child: Column(
            children: [
              if (_selectedFile != null) ...[
                const Icon(Icons.check_circle, size: 40, color: Colors.green),
                const SizedBox(height: 12),
                Text(
                  'เลือกไฟล์แล้ว',
                  style: GoogleFonts.sarabun(
                    fontWeight: FontWeight.w600,
                    fontSize: 14,
                    color: const Color(0xFF334155),
                  ),
                ),
                Text(
                  _selectedFile!.name,
                  style: GoogleFonts.sarabun(
                    fontSize: 12,
                    color: const Color(0xFF64748B),
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ] else ...[
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF1F5F9), // Light purple bg
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.cloud_upload_rounded,
                    color: Color(0xFF6366F1),
                    size: 24,
                  ),
                ),
                const SizedBox(height: 12),
                Text(
                  _isSickLeave ? 'อัปโหลดใบรับรองแพทย์' : 'อัปโหลดเอกสารแนบ',
                  style: GoogleFonts.kanit(
                    fontWeight: FontWeight.w600,
                    fontSize: 14,
                    color: const Color(0xFF1E293B),
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'PDF หรือ JPG, ขนาดไม่เกิน 5MB',
                  style: GoogleFonts.sarabun(
                    fontSize: 12,
                    color: const Color(0xFF94A3B8),
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSubmitButton() {
    return SizedBox(
      width: double.infinity,
      height: 56,
      child: ElevatedButton(
        onPressed: _isLoading ? null : _submit,
        style: ElevatedButton.styleFrom(
          backgroundColor: const Color(0xFF7C3AED), // Purple
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          elevation: 0,
        ),
        child: _isLoading
            ? const CircularProgressIndicator(color: Colors.white)
            : Text(
                'ส่งใบลา',
                style: GoogleFonts.kanit(
                  fontSize: 16,
                  fontWeight: FontWeight.w600,
                  color: Colors.white,
                ),
              ),
      ),
    );
  }
}

class DashedBorderPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final Paint paint = Paint()
      ..color = const Color(0xFFCBD5E1)
      ..strokeWidth = 1.5
      ..style = PaintingStyle.stroke;

    const double dashWidth = 8;
    const double dashSpace = 4;
    final double radius = 12;

    final RRect rrect = RRect.fromRectAndRadius(
      Rect.fromLTWH(0, 0, size.width, size.height),
      Radius.circular(radius),
    );

    final Path path = Path()..addRRect(rrect);

    // Simple dashed implementation for path
    Path dashPath = Path();
    double distance = 0.0;
    for (PathMetric pathMetric in path.computeMetrics()) {
      while (distance < pathMetric.length) {
        dashPath.addPath(
          pathMetric.extractPath(distance, distance + dashWidth),
          Offset.zero,
        );
        distance += dashWidth + dashSpace;
      }
    }

    canvas.drawPath(dashPath, paint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
