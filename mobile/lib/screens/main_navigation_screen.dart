import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/app_theme.dart';
import '../providers/auth_provider.dart';
import '../providers/leave_provider.dart';
import 'home_screen.dart';
import 'leave_history_screen.dart';
import 'approvals_screen.dart';
import 'profile_screen.dart';
import 'reports_screen.dart';

class MainNavigationScreen extends StatefulWidget {
  const MainNavigationScreen({super.key});

  @override
  State<MainNavigationScreen> createState() => _MainNavigationScreenState();
}

class _MainNavigationScreenState extends State<MainNavigationScreen> {
  int _selectedIndex = 0;

  @override
  Widget build(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).user;
    final leaveProvider = Provider.of<LeaveProvider>(context);

    final bool isApprover =
        user?.role == 'supervisor' ||
        user?.role == 'manager' ||
        user?.role == 'admin' ||
        user?.role == 'director' ||
        user?.role == 'deputy_director' ||
        user?.role == 'department_head';

    final bool isAdmin =
        user?.role == 'admin' ||
        user?.role == 'director' ||
        user?.role == 'deputy_director' ||
        user?.role == 'department_head';

    final List<Widget> screens = [
      const HomeScreen(),
      const LeaveHistoryScreen(),
      if (isApprover) const ApprovalsScreen(),
      if (isAdmin) const ReportsScreen(),
      const ProfileScreen(),
    ];

    return Scaffold(
      extendBody: true, // Crucial for floating nav bar effect
      body: IndexedStack(index: _selectedIndex, children: screens),
      bottomNavigationBar: _buildFloatingNavigationBar(
        isApprover,
        isAdmin,
        leaveProvider,
      ),
    );
  }

  Widget _buildFloatingNavigationBar(
    bool isApprover,
    bool isAdmin,
    LeaveProvider leaveProvider,
  ) {
    int currentIndex = 0;
    return Container(
      height: 90,
      margin: const EdgeInsets.fromLTRB(20, 0, 20, 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(35),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 30,
            spreadRadius: 0,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(35),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 10),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildNavItem(currentIndex++, Icons.home_rounded, 'หน้าหลัก'),
              _buildNavItem(currentIndex++, Icons.history_rounded, 'ประวัติ'),
              if (isApprover)
                _buildNavItem(
                  currentIndex++,
                  Icons.fact_check_rounded,
                  'อนุมัติ',
                  hasBadge: leaveProvider.pendingApprovals.isNotEmpty,
                ),
              if (isAdmin)
                _buildNavItem(
                  currentIndex++,
                  Icons.insert_chart_rounded,
                  'รายงาน',
                ),
              _buildNavItem(currentIndex++, Icons.person_rounded, 'โปรไฟล์'),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(
    int index,
    IconData icon,
    String label, {
    bool hasBadge = false,
  }) {
    final isSelected = _selectedIndex == index;

    return Expanded(
      child: GestureDetector(
        onTap: () {
          setState(() {
            _selectedIndex = index;
          });
        },
        behavior: HitTestBehavior.opaque,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            AnimatedContainer(
              duration: const Duration(milliseconds: 300),
              curve: Curves.easeOutCubic,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              decoration: BoxDecoration(
                color: isSelected
                    ? AppTheme.primary.withOpacity(0.12)
                    : Colors.transparent,
                borderRadius: BorderRadius.circular(20),
              ),
              child: Stack(
                clipBehavior: Clip.none,
                children: [
                  Icon(
                    icon,
                    color: isSelected
                        ? AppTheme.primary
                        : AppTheme.textSub.withOpacity(0.4),
                    size: 24,
                  ),
                  if (hasBadge)
                    Positioned(
                      right: -2,
                      top: -2,
                      child: Container(
                        width: 9,
                        height: 9,
                        decoration: BoxDecoration(
                          color: AppTheme.error,
                          shape: BoxShape.circle,
                          border: Border.all(color: Colors.white, width: 2),
                        ),
                      ),
                    ),
                ],
              ),
            ),
            const SizedBox(height: 4),
            Text(
              label,
              style: TextStyle(
                color: isSelected
                    ? AppTheme.primary
                    : AppTheme.textSub.withOpacity(0.6),
                fontSize: 10,
                fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
