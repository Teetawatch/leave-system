import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../providers/auth_provider.dart';
import '../providers/leave_provider.dart';
import '../providers/guard_change_provider.dart';
import 'home_screen.dart';
import 'leave_history_screen.dart'; // This now contains ActivityScreen
import 'approvals_screen.dart';
import 'profile_screen.dart';
import 'reports_screen.dart';

class MainNavigationScreen extends StatefulWidget {
  final int initialIndex;
  final int initialHistoryTab;
  const MainNavigationScreen({
    super.key,
    this.initialIndex = 0,
    this.initialHistoryTab = 0,
  });

  @override
  State<MainNavigationScreen> createState() => _MainNavigationScreenState();
}

class _MainNavigationScreenState extends State<MainNavigationScreen> {
  int _selectedIndex = 0;

  @override
  void initState() {
    super.initState();
    _selectedIndex = widget.initialIndex;
  }

  @override
  Widget build(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).user;
    final leaveProvider = Provider.of<LeaveProvider>(context);
    final guardProvider = Provider.of<GuardChangeProvider>(context);

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
    // Show Approvals tab if management role OR if has pending guard peer confirmations
    final bool showApprovalsTab =
        isApprover || guardProvider.approvals.isNotEmpty;

    // For stability, we keep the screens list consistent or handle index changes.
    // However, it's safer to just include all screens and hide the tab icon.
    final List<Widget> screens = [
      const HomeScreen(),
      ActivityScreen(initialTabIndex: widget.initialHistoryTab),
      if (showApprovalsTab) const ApprovalsScreen(),
      if (isAdmin) const ReportsScreen(),
      const ProfileScreen(),
    ];

    // Ensure index visibility safety
    int safeIndex = _selectedIndex;
    if (safeIndex >= screens.length) {
      safeIndex = 0;
    }

    return Scaffold(
      extendBody: false,
      body: IndexedStack(index: safeIndex, children: screens),
      bottomNavigationBar: _buildGlassNavigationBar(
        showApprovalsTab,
        isAdmin,
        leaveProvider,
        guardProvider,
      ),
    );
  }

  Widget _buildGlassNavigationBar(
    bool showApprovalsTab,
    bool isAdmin,
    LeaveProvider leaveProvider,
    GuardChangeProvider guardProvider,
  ) {
    int currentIndex = 0;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Container(
      margin: EdgeInsets.zero,
      decoration: BoxDecoration(
        color: isDark
            ? AppTheme.surfaceDark.withOpacity(0.85)
            : Colors.white.withOpacity(0.85),
        borderRadius: const BorderRadius.vertical(top: Radius.circular(30)),
        border: Border(
          top: BorderSide(
            color: isDark
                ? Colors.white.withOpacity(0.1)
                : Colors.white.withOpacity(0.5),
            width: 1.5,
          ),
        ),
        boxShadow: [
          BoxShadow(
            color: AppTheme.primary.withOpacity(0.15),
            blurRadius: 30,
            spreadRadius: -5,
            offset: const Offset(0, -5),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: const BorderRadius.vertical(top: Radius.circular(30)),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 12, sigmaY: 12),
          child: SafeArea(
            top: false,
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  _buildNavItem(
                    currentIndex++,
                    Icons.grid_view_rounded,
                    'หน้าหลัก',
                  ),
                  _buildNavItem(
                    currentIndex++,
                    Icons.calendar_today_rounded,
                    'ประวัติ',
                  ),
                  if (showApprovalsTab)
                    _buildNavItem(
                      currentIndex++,
                      Icons.assignment_turned_in_rounded,
                      'อนุมัติ',
                      badgeCount:
                          leaveProvider.pendingApprovals.length +
                          guardProvider.approvals.length,
                    ),
                  if (isAdmin)
                    _buildNavItem(
                      currentIndex++,
                      Icons.insert_chart_rounded, // Better report icon
                      'รายงาน',
                    ),
                  _buildNavItem(
                    currentIndex++,
                    Icons.person_rounded,
                    'โปรไฟล์',
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(
    int index,
    IconData icon,
    String label, {
    int badgeCount = 0,
  }) {
    final isSelected = _selectedIndex == index;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final inactiveColor = isDark ? AppTheme.textSubDark : AppTheme.textSub;

    return Expanded(
      child: InkWell(
        onTap: () => setState(() => _selectedIndex = index),
        borderRadius: BorderRadius.circular(40),
        splashColor: AppTheme.primary.withOpacity(0.1),
        highlightColor: Colors.transparent,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 400),
          curve: Curves.fastOutSlowIn,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              AnimatedScale(
                scale: isSelected ? 1.15 : 1.0,
                duration: const Duration(milliseconds: 300),
                child: Stack(
                  clipBehavior: Clip.none,
                  children: [
                    Icon(
                      icon,
                      color: isSelected
                          ? AppTheme.primary
                          : inactiveColor.withOpacity(0.4),
                      size: 26,
                    ),
                    if (badgeCount > 0)
                      Positioned(
                        right: -6,
                        top: -4,
                        child: Container(
                          padding: const EdgeInsets.all(4),
                          decoration: const BoxDecoration(
                            color: Color(0xFFF43F5E),
                            shape: BoxShape.circle,
                          ),
                          constraints: const BoxConstraints(
                            minWidth: 16,
                            minHeight: 16,
                          ),
                          child: Text(
                            '$badgeCount',
                            textAlign: TextAlign.center,
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 8,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ),
                  ],
                ),
              ),
              const SizedBox(height: 6),
              AnimatedOpacity(
                opacity: isSelected ? 1.0 : 0.6,
                duration: const Duration(milliseconds: 300),
                child: Text(
                  label,
                  style: TextStyle(
                    color: isSelected ? AppTheme.primary : inactiveColor,
                    fontSize: 11,
                    fontWeight: isSelected ? FontWeight.w900 : FontWeight.w600,
                    letterSpacing: -0.2,
                  ),
                ),
              ),
              if (isSelected)
                Container(
                  margin: const EdgeInsets.only(top: 4),
                  width: 4,
                  height: 4,
                  decoration: const BoxDecoration(
                    color: AppTheme.primary,
                    shape: BoxShape.circle,
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}
