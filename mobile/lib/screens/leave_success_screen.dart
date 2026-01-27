import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:intl/date_symbol_data_local.dart';
import '../models/leave_type_model.dart';

import 'main_navigation_screen.dart';

class LeaveSuccessScreen extends StatelessWidget {
  final LeaveType leaveType;
  final DateTime startDate;
  final DateTime endDate;

  const LeaveSuccessScreen({
    super.key,
    required this.leaveType,
    required this.startDate,
    required this.endDate,
  });

  String _formatDateRange(DateTime start, DateTime end) {
    initializeDateFormatting('th_TH', null);
    final thDateFormat = DateFormat('d MMM yyyy', 'th_TH');

    // Check if dates are same day
    if (start.year == end.year &&
        start.month == end.month &&
        start.day == end.day) {
      return thDateFormat.format(start);
    }

    // If same month and year
    if (start.year == end.year && start.month == end.month) {
      return '${start.day} - ${thDateFormat.format(end)}';
    }

    return '${thDateFormat.format(start)} - ${thDateFormat.format(end)}';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              const Spacer(),
              // Success Icon
              Container(
                width: 120,
                height: 120,
                decoration: BoxDecoration(
                  color: const Color(0xFFF3E8FF), // Light purple
                  shape: BoxShape.circle,
                ),
                child: const Center(
                  child: Icon(
                    Icons.check_rounded,
                    color: Color(0xFF9333EA), // Purple
                    size: 64,
                  ),
                ),
              ),
              const SizedBox(height: 32),

              // Title
              Text(
                'ส่งคำขอสำเร็จ',
                style: GoogleFonts.kanit(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                  color: Colors.black,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 12),

              // Description
              Text(
                'ส่งคำขอลาของคุณเรียบร้อยแล้ว\nและกำลังรอการอนุมัติ',
                style: GoogleFonts.sarabun(
                  fontSize: 16,
                  color: const Color(0xFF64748B),
                  height: 1.5,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 40),

              // Details Card
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(24),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.05),
                      blurRadius: 20,
                      offset: const Offset(0, 10),
                    ),
                  ],
                ),
                child: Column(
                  children: [
                    _buildDetailRow('ประเภทการลา', leaveType.name),
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      child: Divider(color: Colors.grey[100]),
                    ),
                    _buildDetailRow(
                      'วันที่ลา',
                      _formatDateRange(startDate, endDate),
                    ),
                  ],
                ),
              ),

              const Spacer(),

              // Back to Home Button
              SizedBox(
                width: double.infinity,
                height: 56,
                child: ElevatedButton(
                  onPressed: () {
                    Navigator.of(context).pushAndRemoveUntil(
                      MaterialPageRoute(
                        builder: (context) => const MainNavigationScreen(),
                      ),
                      (route) => false,
                    );
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF6366F1),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                    elevation: 0,
                  ),
                  child: Text(
                    'กลับหน้าหลัก',
                    style: GoogleFonts.kanit(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                      color: Colors.white,
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 16),

              // View History Button
              TextButton(
                onPressed: () {
                  // Navigate to History Screen (removing all previous routes except home maybe? or just push)
                  // Better to go to Home then push History or just push History
                  // For now, let's push replacement or clear until home and push history.
                  // User flow: Success -> History -> Back -> Home
                  Navigator.of(context).pushAndRemoveUntil(
                    MaterialPageRoute(
                      builder: (context) =>
                          const MainNavigationScreen(initialIndex: 1),
                    ),
                    (route) => false,
                  );
                },
                child: Text(
                  'ดูประวัติการลา',
                  style: GoogleFonts.kanit(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: Colors.black,
                  ),
                ),
              ),
              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: GoogleFonts.sarabun(
            fontSize: 14,
            color: const Color(0xFF94A3B8), // Muted text
          ),
        ),
        Text(
          value,
          style: GoogleFonts.sarabun(
            fontSize: 16,
            fontWeight: FontWeight.w600,
            color: const Color(0xFF1E293B), // Dark text
          ),
        ),
      ],
    );
  }
}
