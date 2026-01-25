import 'package:flutter/material.dart';
import '../config/app_theme.dart';
import 'leave_report_screen.dart';
import 'guard_report_screen.dart';

class ReportsScreen extends StatelessWidget {
  const ReportsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('รายงานสรุป'),
        backgroundColor: Colors.white,
        elevation: 0,
        foregroundColor: AppTheme.textMain,
      ),
      body: ListView(
        padding: const EdgeInsets.all(24),
        children: [
          _buildReportCard(
            context,
            title: 'รายงานการลา',
            subtitle: 'สรุปการลาทั้งหมด สถิติ และรายละเอียด',
            icon: Icons.summarize_rounded,
            color: AppTheme.primary,
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const LeaveReportScreen()),
            ),
          ),
          const SizedBox(height: 16),
          _buildReportCard(
            context,
            title: 'รายงานการเปลี่ยนเวรยาม',
            subtitle: 'สรุปการขอเปลี่ยนเวรและสถานะการอนุมัติ',
            icon: Icons.security_rounded,
            color: AppTheme.secondary,
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const GuardReportScreen()),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildReportCard(
    BuildContext context, {
    required String title,
    required String subtitle,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(24),
      child: Container(
        padding: const EdgeInsets.all(24),
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
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Icon(icon, color: color, size: 32),
            ),
            const SizedBox(width: 20),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: AppTheme.textMain,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    subtitle,
                    style: TextStyle(fontSize: 13, color: AppTheme.textSub),
                  ),
                ],
              ),
            ),
            Icon(
              Icons.chevron_right_rounded,
              color: AppTheme.textSub.withOpacity(0.5),
            ),
          ],
        ),
      ),
    );
  }
}
