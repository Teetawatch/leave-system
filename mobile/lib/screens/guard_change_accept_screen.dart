import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../models/guard_change_model.dart';
// Assuming this exists

class GuardChangeAcceptScreen extends StatefulWidget {
  final GuardChangeRequest request;

  const GuardChangeAcceptScreen({super.key, required this.request});

  @override
  State<GuardChangeAcceptScreen> createState() =>
      _GuardChangeAcceptScreenState();
}

class _GuardChangeAcceptScreenState extends State<GuardChangeAcceptScreen> {
  bool _isProcessing = false;

  void _handleAccept() async {
    // This logic handles accepting the shift.
    // If the API requires a signature or just a confirmation, implementing basic confirmation here.
    setState(() => _isProcessing = true);

    // Simulating API call or real call if provider available
    // For now we will allow the user to confirm receiving this shift.
    // In a real flow, this might call provider.approveRequest or provider.acceptRequest

    // Based on GuardChangeApprovalsScreen, it uses provider.approveRequest.
    // But since this is likely the "Replacement User" accepting before the "Approver",
    // we might need a different method. For now, assuming approval logic is similar or just a mock for UI.
    // However, the user asked for design. I'll include the button action to just pop or show success.

    await Future.delayed(const Duration(seconds: 1));
    if (!mounted) return;

    // Show success and pop
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('ยืนยันการรับเวรเรียบร้อยแล้ว'),
        backgroundColor: Colors.green,
      ),
    );
    Navigator.pop(context, true);
  }

  @override
  Widget build(BuildContext context) {
    // Determine status color
    // Using blue/primary color scheme from the image

    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9), // Light gray background
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, color: Colors.black),
          onPressed: () => Navigator.pop(context),
        ),
        title: const Text(
          'แลกเปลี่ยนเวรยาม', // Shift Swap
          style: TextStyle(
            color: Colors.black,
            fontWeight: FontWeight.w700,
            fontSize: 20,
          ),
        ),
        centerTitle: true,
        systemOverlayStyle: SystemUiOverlayStyle.dark,
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 12.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              const SizedBox(height: 10),
              const Text(
                'ยอมรับการเปลี่ยนเวร?',
                style: TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.w800,
                  color: Color(0xFF1E293B),
                ),
              ),
              const SizedBox(height: 8),
              Text(
                'โปรดตรวจสอบรายละเอียดคำขอจากเพื่อนร่วมงาน\nที่โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 14,
                  color: const Color(0xFF64748B),
                  height: 1.5,
                ),
              ),
              const SizedBox(height: 32),

              // Main Card with arrow overlay
              Stack(
                alignment: Alignment.center,
                clipBehavior: Clip.none,
                children: [
                  Column(
                    children: [
                      // Top Section: Received Shift (Shift Date etc)
                      // Mapping "Your Shift" from image to "เวรที่คุณจะได้รับ"
                      _buildShiftCard(
                        title: 'เวรที่คุณได้รับ',
                        icon: Icons.person_outline_rounded,
                        date: widget.request.formattedDutyDate,
                        time:
                            '08:00 - 16:00 (8ชม.)', // Placeholder time as it's not in model
                        location: widget.request.dutyPositionThai,
                        imageAsset: 'assets/images/warehouse.png',
                        isTop: true,
                      ),
                      const SizedBox(height: 16), // Gap for the button
                      // Bottom Section: Requestor (Their Shift)
                      // Mapping "Their Shift" from image to "ผู้ขอเปลี่ยนเวร"
                      _buildRequesterCard(
                        title: 'ผู้ขอเปลี่ยนเวร',
                        icon: Icons.people_alt_outlined,
                        date: widget.request.formattedDutyDate,
                        time: '16:00 - 00:00 (8ชม.)', // Placeholder time
                        name: widget.request.user?.fullName ?? 'ไม่ระบุชื่อ',
                        rank:
                            widget.request.user?.rank ??
                            'พลทหาร', // Assuming rank exists or placeholder
                        userImage: widget.request.user?.avatarUrl,
                      ),
                    ],
                  ),

                  // The Swap Icon
                  Positioned(
                    top: 155, // Adjusted manually based on card height
                    child: Container(
                      width: 50,
                      height: 50,
                      decoration: BoxDecoration(
                        color: const Color(0xFF1D77FF), // Bright Blue
                        shape: BoxShape.circle,
                        border: Border.all(
                          color: const Color(0xFFF1F5F9),
                          width: 4,
                        ),
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFF1D77FF).withOpacity(0.3),
                            blurRadius: 10,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: const Icon(
                        Icons.swap_vert_rounded,
                        color: Colors.white,
                        size: 28,
                      ),
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 24),

              // Reason Section
              Align(
                alignment: Alignment.centerLeft,
                child: Padding(
                  padding: const EdgeInsets.only(left: 4.0, bottom: 8.0),
                  child: Text(
                    'เหตุผลของเพื่อนร่วมงาน',
                    style: TextStyle(
                      fontWeight: FontWeight.w700,
                      fontSize: 16,
                      color: Color(0xFF1E293B),
                    ),
                  ),
                ),
              ),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: const Color(0xFFF0F9FF), // Light blue tint
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFE0F2FE)),
                ),
                child: Text(
                  widget.request.remarks != null &&
                          widget.request.remarks!.isNotEmpty
                      ? '"${widget.request.remarks}"'
                      : '"ไม่มีเหตุผลระบุ"',
                  style: TextStyle(
                    color: Color(0xFF0369A1), // Darker blue text
                    fontSize: 14,
                    height: 1.5,
                    fontStyle: FontStyle.italic,
                  ),
                ),
              ),

              const SizedBox(height: 32),

              // Buttons
              SizedBox(
                width: double.infinity,
                height: 56,
                child: ElevatedButton(
                  onPressed: _isProcessing ? null : _handleAccept,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF1D77FF),
                    foregroundColor: Colors.white,
                    elevation: 0,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                  ),
                  child: _isProcessing
                      ? const SizedBox(
                          width: 24,
                          height: 24,
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                      : const Text(
                          'ยืนยันการยอมรับ',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                ),
              ),
              const SizedBox(height: 12),
              SizedBox(
                width: double.infinity,
                height: 56,
                child: TextButton(
                  onPressed: () => Navigator.pop(context),
                  style: TextButton.styleFrom(
                    backgroundColor: Colors.white,
                    foregroundColor: const Color(0xFF1E293B),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                      side: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                  ),
                  child: const Text(
                    'ย้อนกลับ',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
                  ),
                ),
              ),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildShiftCard({
    required String title,
    required IconData icon,
    required String date,
    required String time,
    required String location,
    required String imageAsset,
    bool isTop = false,
  }) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(
        20,
        20,
        20,
        30,
      ), // Extra padding bottom for overlap
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(
          top: Radius.circular(24),
          bottom: Radius.circular(4),
        ), // Slightly rounded bottom only if top
        boxShadow: [
          BoxShadow(
            color: Color.fromRGBO(0, 0, 0, 0.05),
            blurRadius: 10,
            offset: Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, size: 20, color: const Color(0xFF1D77FF)),
              const SizedBox(width: 8),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w800,
                  color: Color(0xFF0F172A),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'วันและเวลา',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: Color(0xFF64748B),
                        letterSpacing: 0.5,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      date,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: Color(0xFF1E293B),
                      ),
                    ),
                    Text(
                      time,
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w500,
                        color: Color(0xFF334155),
                      ),
                    ),
                    const SizedBox(height: 12),
                    Text(
                      'สถานที่',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: Color(0xFF64748B),
                        letterSpacing: 0.5,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      location,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: Color(0xFF1E293B),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 12),
              Container(
                width: 100,
                height: 80,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(12),
                  image: DecorationImage(
                    image: AssetImage(imageAsset),
                    fit: BoxFit.cover,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildRequesterCard({
    required String title,
    required IconData icon,
    required String date,
    required String time,
    required String name,
    required String rank,
    String? userImage,
  }) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(20, 30, 20, 20), // Extra padding top
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.all(Radius.circular(24)),
        boxShadow: [
          BoxShadow(
            color: Color.fromRGBO(0, 0, 0, 0.05),
            blurRadius: 10,
            offset: Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, size: 20, color: const Color(0xFF1D77FF)),
              const SizedBox(width: 8),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w800,
                  color: Color(0xFF0F172A),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'วันและเวลา',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: Color(0xFF64748B),
                        letterSpacing: 0.5,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      date,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: Color(0xFF1E293B),
                      ),
                    ),
                    Text(
                      time,
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w500,
                        color: Color(0xFF334155),
                      ),
                    ),
                    const SizedBox(height: 12),
                    Text(
                      'เพื่อนร่วมงาน',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: Color(0xFF64748B),
                        letterSpacing: 0.5,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      name,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: Color(0xFF1E293B),
                      ),
                    ),
                    Text(
                      rank,
                      style: const TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w400,
                        color: Color(0xFF64748B),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 12),
              Container(
                width: 90,
                height: 100,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(12),
                  color: Colors.grey[200],
                  image: userImage != null
                      ? DecorationImage(
                          image: NetworkImage(userImage),
                          fit: BoxFit.cover,
                        )
                      : null,
                ),
                child: userImage == null
                    ? Icon(Icons.person, size: 40, color: Colors.grey[400])
                    : null,
              ),
            ],
          ),
        ],
      ),
    );
  }
}
