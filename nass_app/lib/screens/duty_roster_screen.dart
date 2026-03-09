import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../config/app_theme.dart';
import '../models/duty_roster.dart';
import '../services/guard_duty_service.dart';

class DutyRosterScreen extends StatefulWidget {
  const DutyRosterScreen({super.key});

  @override
  State<DutyRosterScreen> createState() => _DutyRosterScreenState();
}

class _DutyRosterScreenState extends State<DutyRosterScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  // Today tab
  List<TodayGuardEntry> _todayEntries = [];
  bool _todayLoading = true;

  // Monthly tab
  List<DutyRosterDay> _monthlyDays = [];
  bool _monthlyLoading = true;
  late int _selectedYear;
  late int _selectedMonth;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    final now = DateTime.now();
    _selectedYear = now.year;
    _selectedMonth = now.month;
    _loadToday();
    _loadMonthly();
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) setState(() {});
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadToday() async {
    setState(() => _todayLoading = true);
    final today = DateFormat('yyyy-MM-dd').format(DateTime.now());
    final data = await GuardDutyService.getByDate(today);
    if (mounted) setState(() { _todayEntries = data; _todayLoading = false; });
  }

  Future<void> _loadMonthly() async {
    setState(() => _monthlyLoading = true);
    final data = await GuardDutyService.getMonthly(_selectedYear, _selectedMonth);
    if (mounted) setState(() { _monthlyDays = data; _monthlyLoading = false; });
  }

  void _prevMonth() {
    setState(() {
      if (_selectedMonth == 1) { _selectedMonth = 12; _selectedYear--; }
      else _selectedMonth--;
    });
    _loadMonthly();
  }

  void _nextMonth() {
    setState(() {
      if (_selectedMonth == 12) { _selectedMonth = 1; _selectedYear++; }
      else _selectedMonth++;
    });
    _loadMonthly();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      body: NestedScrollView(
        headerSliverBuilder: (context, _) => [
          SliverAppBar(
            pinned: true,
            expandedHeight: 120,
            backgroundColor: AppTheme.primaryDark,
            flexibleSpace: FlexibleSpaceBar(
              background: Container(
                decoration: const BoxDecoration(gradient: AppTheme.primaryGradient),
                child: SafeArea(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 16, 20, 0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(10),
                              decoration: BoxDecoration(
                                color: Colors.white.withValues(alpha: 0.15),
                                borderRadius: BorderRadius.circular(14),
                              ),
                              child: const Icon(Icons.shield_rounded, color: Colors.white, size: 22),
                            ),
                            const SizedBox(width: 12),
                            Text(
                              'ตารางเวรยาม',
                              style: GoogleFonts.prompt(
                                color: Colors.white,
                                fontSize: 20,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
            bottom: TabBar(
              controller: _tabController,
              labelColor: Colors.white,
              unselectedLabelColor: Colors.white54,
              indicatorColor: Colors.white,
              indicatorWeight: 3,
              labelStyle: GoogleFonts.prompt(fontSize: 14, fontWeight: FontWeight.w600),
              unselectedLabelStyle: GoogleFonts.prompt(fontSize: 14),
              tabs: const [
                Tab(text: 'วันนี้'),
                Tab(text: 'รายเดือน'),
              ],
            ),
          ),
        ],
        body: TabBarView(
          controller: _tabController,
          children: [
            _buildTodayTab(),
            _buildMonthlyTab(),
          ],
        ),
      ),
    );
  }

  // ---------------------------------------------------------------------------
  // TODAY TAB
  // ---------------------------------------------------------------------------

  Widget _buildTodayTab() {
    final now = DateTime.now();
    final thaiDate = DateFormat('EEEE ที่ d MMMM yyyy', 'th').format(now);

    return RefreshIndicator(
      onRefresh: _loadToday,
      color: AppTheme.primary,
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          // Date header
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              gradient: AppTheme.primaryGradient,
              borderRadius: BorderRadius.circular(18),
            ),
            child: Row(
              children: [
                const Icon(Icons.today_rounded, color: Colors.white, size: 22),
                const SizedBox(width: 10),
                Text(
                  thaiDate,
                  style: GoogleFonts.prompt(
                    color: Colors.white,
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),

          if (_todayLoading)
            const Center(
              child: Padding(
                padding: EdgeInsets.all(40),
                child: CircularProgressIndicator(color: AppTheme.primary),
              ),
            )
          else if (_todayEntries.isEmpty)
            _buildEmptyState('ไม่มีข้อมูลเวรวันนี้', 'ยังไม่มีการกำหนดตารางเวร\nสำหรับวันนี้')
          else
            ..._todayEntries.map((e) => _buildGuardCard(e)),
        ],
      ),
    );
  }

  Widget _buildGuardCard(TodayGuardEntry entry) {
    final positionIcon = _positionIcon(entry.positionKey);
    final positionColor = _positionColor(entry.positionKey);

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(20),
        boxShadow: AppTheme.softShadow,
        border: Border.all(color: positionColor.withValues(alpha: 0.15)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [positionColor, positionColor.withValues(alpha: 0.7)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: positionColor.withValues(alpha: 0.3),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Icon(positionIcon, color: Colors.white, size: 24),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  entry.positionLabel,
                  style: GoogleFonts.prompt(
                    fontSize: 12,
                    color: positionColor,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  entry.displayName,
                  style: GoogleFonts.prompt(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.textPrimary,
                  ),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(
              color: AppTheme.successLight,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Text(
              'เข้าเวร',
              style: GoogleFonts.prompt(
                fontSize: 11,
                fontWeight: FontWeight.w600,
                color: AppTheme.success,
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ---------------------------------------------------------------------------
  // MONTHLY TAB
  // ---------------------------------------------------------------------------

  Widget _buildMonthlyTab() {
    final monthName = DateFormat('MMMM yyyy', 'th').format(
      DateTime(_selectedYear, _selectedMonth),
    );

    return RefreshIndicator(
      onRefresh: _loadMonthly,
      color: AppTheme.primary,
      child: Column(
        children: [
          // Month navigator
          Container(
            color: AppTheme.surface,
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            child: Row(
              children: [
                IconButton(
                  onPressed: _prevMonth,
                  icon: const Icon(Icons.chevron_left_rounded, color: AppTheme.primary),
                  style: IconButton.styleFrom(
                    backgroundColor: AppTheme.primary.withValues(alpha: 0.08),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                ),
                Expanded(
                  child: Text(
                    monthName,
                    textAlign: TextAlign.center,
                    style: GoogleFonts.prompt(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                      color: AppTheme.textPrimary,
                    ),
                  ),
                ),
                IconButton(
                  onPressed: _nextMonth,
                  icon: const Icon(Icons.chevron_right_rounded, color: AppTheme.primary),
                  style: IconButton.styleFrom(
                    backgroundColor: AppTheme.primary.withValues(alpha: 0.08),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                ),
              ],
            ),
          ),
          const Divider(height: 1),
          Expanded(
            child: _monthlyLoading
                ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
                : _monthlyDays.isEmpty
                    ? _buildEmptyState('ไม่มีข้อมูลตารางเวร', 'ยังไม่มีการกำหนดตารางเวร\nสำหรับเดือนนี้')
                    : ListView.builder(
                        padding: const EdgeInsets.all(16),
                        itemCount: _monthlyDays.length,
                        itemBuilder: (_, i) => _buildDayCard(_monthlyDays[i]),
                      ),
          ),
        ],
      ),
    );
  }

  Widget _buildDayCard(DutyRosterDay day) {
    DateTime? date;
    try { date = DateTime.parse(day.date); } catch (_) {}

    final isToday = date != null &&
        date.year == DateTime.now().year &&
        date.month == DateTime.now().month &&
        date.day == DateTime.now().day;

    final dayLabel = date != null
        ? DateFormat('d', 'th').format(date)
        : '-';
    final dayName = date != null
        ? DateFormat('E', 'th').format(date)
        : '';
    final fullDate = date != null
        ? DateFormat('d MMMM yyyy', 'th').format(date)
        : day.date;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: isToday ? AppTheme.primary.withValues(alpha: 0.04) : AppTheme.surface,
        borderRadius: BorderRadius.circular(20),
        boxShadow: AppTheme.softShadow,
        border: Border.all(
          color: isToday ? AppTheme.primary.withValues(alpha: 0.3) : Colors.transparent,
          width: isToday ? 1.5 : 0,
        ),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Date badge
            Container(
              width: 52,
              height: 58,
              decoration: BoxDecoration(
                gradient: isToday ? AppTheme.primaryGradient : null,
                color: isToday ? null : AppTheme.primary.withValues(alpha: 0.08),
                borderRadius: BorderRadius.circular(14),
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    dayLabel,
                    style: GoogleFonts.prompt(
                      fontSize: 22,
                      fontWeight: FontWeight.w700,
                      color: isToday ? Colors.white : AppTheme.primary,
                      height: 1,
                    ),
                  ),
                  Text(
                    dayName,
                    style: GoogleFonts.prompt(
                      fontSize: 11,
                      color: isToday ? Colors.white70 : AppTheme.textMuted,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 14),
            // Duty officers
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (isToday)
                    Padding(
                      padding: const EdgeInsets.only(bottom: 6),
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: AppTheme.primary,
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          'วันนี้',
                          style: GoogleFonts.prompt(fontSize: 10, color: Colors.white, fontWeight: FontWeight.w600),
                        ),
                      ),
                    ),
                  Text(
                    fullDate,
                    style: GoogleFonts.prompt(
                      fontSize: 12,
                      color: AppTheme.textMuted,
                    ),
                  ),
                  const SizedBox(height: 8),
                  if (day.seniorDutyOfficer != null)
                    _buildOfficerRow(
                      Icons.military_tech_rounded,
                      'นายทหารเวรอาวุโส',
                      day.seniorDutyOfficer!.displayName,
                      const Color(0xFF7C3AED),
                    ),
                  if (day.dutyOfficer != null)
                    _buildOfficerRow(
                      Icons.shield_rounded,
                      'นายทหารเวร',
                      day.dutyOfficer!.displayName,
                      AppTheme.primary,
                    ),
                  if (day.assistantDutyOfficer != null)
                    _buildOfficerRow(
                      Icons.person_outlined,
                      'ผู้ช่วยนายทหารเวร',
                      day.assistantDutyOfficer!.displayName,
                      AppTheme.secondary,
                    ),
                  if (day.seniorDutyOfficer == null &&
                      day.dutyOfficer == null &&
                      day.assistantDutyOfficer == null)
                    Text(
                      'ยังไม่มีข้อมูล',
                      style: AppTheme.body(12, color: AppTheme.textMuted),
                    ),
                  if (day.notes != null && day.notes!.isNotEmpty)
                    Padding(
                      padding: const EdgeInsets.only(top: 6),
                      child: Text(
                        'หมายเหตุ: ${day.notes}',
                        style: AppTheme.body(11, color: AppTheme.textMuted),
                      ),
                    ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildOfficerRow(IconData icon, String position, String name, Color color) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 4),
      child: Row(
        children: [
          Icon(icon, size: 14, color: color),
          const SizedBox(width: 6),
          Expanded(
            child: RichText(
              text: TextSpan(
                style: GoogleFonts.prompt(fontSize: 12, color: AppTheme.textSecondary),
                children: [
                  TextSpan(
                    text: '$position: ',
                    style: const TextStyle(color: AppTheme.textMuted),
                  ),
                  TextSpan(
                    text: name,
                    style: GoogleFonts.prompt(
                      fontWeight: FontWeight.w600,
                      color: AppTheme.textPrimary,
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ---------------------------------------------------------------------------
  // Helpers
  // ---------------------------------------------------------------------------

  Widget _buildEmptyState(String title, String subtitle) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(40),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: AppTheme.primary.withValues(alpha: 0.06),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.shield_outlined, size: 48, color: AppTheme.primary),
            ),
            const SizedBox(height: 20),
            Text(title, style: AppTheme.heading(16)),
            const SizedBox(height: 6),
            Text(
              subtitle,
              textAlign: TextAlign.center,
              style: AppTheme.body(13, color: AppTheme.textMuted),
            ),
          ],
        ),
      ),
    );
  }

  IconData _positionIcon(String key) {
    switch (key) {
      case 'senior_duty_officer': return Icons.military_tech_rounded;
      case 'duty_officer': return Icons.shield_rounded;
      case 'assistant_duty_officer': return Icons.person_rounded;
      default: return Icons.person_rounded;
    }
  }

  Color _positionColor(String key) {
    switch (key) {
      case 'senior_duty_officer': return const Color(0xFF7C3AED);
      case 'duty_officer': return AppTheme.primary;
      case 'assistant_duty_officer': return AppTheme.secondary;
      default: return AppTheme.primary;
    }
  }
}
