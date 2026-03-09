import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../config/app_theme.dart';
import '../providers/auth_provider.dart';
import 'dashboard_screen.dart';
import 'leave/leave_list_screen.dart';
import 'guard_change/guard_change_list_screen.dart';
import 'approval/approval_list_screen.dart';
import 'duty_roster_screen.dart';
import 'profile_screen.dart';

class HomeShell extends StatefulWidget {
  const HomeShell({super.key});

  @override
  State<HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends State<HomeShell> {
  int _currentIndex = 0;

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final canApprove = user?.canApprove ?? false;

    final screens = [
      const DashboardScreen(),
      const LeaveListScreen(),
      const GuardChangeListScreen(),
      const DutyRosterScreen(),
      if (canApprove) const ApprovalListScreen(),
      const ProfileScreen(),
    ];

    // Ensure current index is valid
    if (_currentIndex >= screens.length) {
      _currentIndex = 0;
    }

    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: screens,
      ),
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          color: AppTheme.surface,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.06),
              blurRadius: 20,
              offset: const Offset(0, -4),
            ),
          ],
        ),
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 8),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _buildNavItem(0, Icons.home_rounded, Icons.home_outlined, 'หน้าแรก'),
                _buildNavItem(1, Icons.event_note_rounded, Icons.event_note_outlined, 'การลา'),
                _buildNavItem(2, Icons.swap_horiz_rounded, Icons.swap_horiz_rounded, 'เปลี่ยนเวร'),
                _buildNavItem(3, Icons.shield_rounded, Icons.shield_outlined, 'ตารางเวร'),
                if (canApprove)
                  _buildNavItem(4, Icons.task_alt_rounded, Icons.task_alt_outlined, 'อนุมัติ'),
                _buildNavItem(
                  canApprove ? 5 : 4,
                  Icons.person_rounded,
                  Icons.person_outline_rounded,
                  'โปรไฟล์',
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(int index, IconData activeIcon, IconData inactiveIcon, String label) {
    final isActive = _currentIndex == index;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _currentIndex = index),
        behavior: HitTestBehavior.opaque,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.symmetric(vertical: 6),
          decoration: BoxDecoration(
            color: isActive ? AppTheme.primary.withValues(alpha: 0.08) : Colors.transparent,
            borderRadius: BorderRadius.circular(14),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(
                isActive ? activeIcon : inactiveIcon,
                color: isActive ? AppTheme.primary : AppTheme.textMuted,
                size: 24,
              ),
              const SizedBox(height: 4),
              Text(
                label,
                style: GoogleFonts.prompt(
                  fontSize: 10,
                  fontWeight: isActive ? FontWeight.w600 : FontWeight.w400,
                  color: isActive ? AppTheme.primary : AppTheme.textMuted,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
