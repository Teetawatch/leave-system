import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'home_screen.dart';
import 'guard_change_history_screen.dart';

class GuardChangeSuccessScreen extends StatelessWidget {
  final DateTime dutyDate;
  final String position;
  final String replacementName;

  const GuardChangeSuccessScreen({
    super.key,
    required this.dutyDate,
    required this.position,
    required this.replacementName,
  });

  String _formatDate(DateTime date) {
    initializeDateFormatting('th_TH', null);
    return DateFormat('d MMM yyyy', 'th_TH').format(date);
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
                decoration: const BoxDecoration(
                  color: Color(0xFFEFF6FF), // Light blue for guard change
                  shape: BoxShape.circle,
                ),
                child: const Center(
                  child: Icon(
                    Icons.check_rounded,
                    color: Color(0xFF3B82F6), // Blue
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
                'ส่งคำขอเปลี่ยนเวรยามของคุณเรียบร้อยแล้ว\nและกำลังรอการอนุมัติ',
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
                    _buildDetailRow('วันที่เข้าเวร', _formatDate(dutyDate)),
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      child: Divider(color: Colors.grey[100]),
                    ),
                    _buildDetailRow('ตำแหน่ง', position),
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      child: Divider(color: Colors.grey[100]),
                    ),
                    _buildDetailRow('ผู้เปลี่ยนแทน', replacementName),
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
                        builder: (context) => const HomeScreen(),
                      ),
                      (route) => false,
                    );
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF3B82F6), // Blue
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
                  Navigator.of(context).pushAndRemoveUntil(
                    MaterialPageRoute(builder: (context) => const HomeScreen()),
                    (route) => false,
                  );
                  Navigator.of(context).push(
                    MaterialPageRoute(
                      builder: (context) => const GuardChangeHistoryScreen(),
                    ),
                  );
                },
                child: Text(
                  'ดูประวัติการเปลี่ยนเวร',
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
        Expanded(
          child: Text(
            value,
            textAlign: TextAlign.end,
            style: GoogleFonts.sarabun(
              fontSize: 16,
              fontWeight: FontWeight.w600,
              color: const Color(0xFF1E293B), // Dark text
            ),
            overflow: TextOverflow.ellipsis,
          ),
        ),
      ],
    );
  }
}
