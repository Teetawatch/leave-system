import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../models/leave_request_model.dart';
import '../models/guard_change_model.dart';
import '../providers/leave_provider.dart';
import '../providers/guard_change_provider.dart';
import '../services/pdf_service.dart';
import '../widgets/animated_background.dart';
import 'package:google_fonts/google_fonts.dart';
import 'leave_request_screen.dart';
import 'guard_change_request_screen.dart';

class ActivityScreen extends StatefulWidget {
  const ActivityScreen({super.key});

  @override
  State<ActivityScreen> createState() => _ActivityScreenState();
}

class _ActivityScreenState extends State<ActivityScreen>
    with TickerProviderStateMixin {
  late TabController _tabController;
  late AnimationController _animationController;
  String _leaveFilter = 'all'; // all, pending, approved, rejected
  String _guardFilter = 'all'; // all, pending, completed, cancelled

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1000),
    )..forward();

    WidgetsBinding.instance.addPostFrameCallback((_) {
      _refreshData();
    });

    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) {
        setState(() {});
      }
    });
  }

  void _refreshData() {
    Provider.of<LeaveProvider>(context, listen: false).fetchMyRequests();
    Provider.of<GuardChangeProvider>(context, listen: false).fetchMyRequests();
  }

  @override
  void dispose() {
    _tabController.dispose();
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final leaveProvider = Provider.of<LeaveProvider>(context);
    final guardProvider = Provider.of<GuardChangeProvider>(context);

    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text(
          'รายการกิจกรรม',
          style: TextStyle(
            fontWeight: FontWeight.w900,
            color: Color(0xFF1E293B),
            letterSpacing: -0.5,
          ),
        ),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        systemOverlayStyle: SystemUiOverlayStyle.dark,
        leading: IconButton(
          onPressed: () => Navigator.pop(context),
          icon: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.5),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.arrow_back_ios_new_rounded,
              size: 20,
              color: Color(0xFF1E293B),
            ),
          ),
        ),
      ),
      body: Stack(
        children: [
          // Background
          const Positioned.fill(child: AnimatedBackground()),

          // Content
          SafeArea(
            child: Column(
              children: [
                const SizedBox(height: 10),
                // Custom Tab Bar
                Container(
                  margin: const EdgeInsets.symmetric(
                    horizontal: 24,
                    vertical: 8,
                  ),
                  padding: const EdgeInsets.all(4),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.05),
                        blurRadius: 10,
                        offset: const Offset(0, 4),
                      ),
                    ],
                    border: Border.all(color: Colors.white),
                  ),
                  child: TabBar(
                    controller: _tabController,
                    indicator: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFF6366F1), Color(0xFF8B5CF6)],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFF6366F1).withOpacity(0.3),
                          blurRadius: 8,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    labelColor: Colors.white,
                    unselectedLabelColor: const Color(0xFF64748B),
                    labelStyle: const TextStyle(
                      fontWeight: FontWeight.w700,
                      fontSize: 14,
                      fontFamily: 'Kanit',
                    ),
                    unselectedLabelStyle: const TextStyle(
                      fontWeight: FontWeight.w600,
                      fontSize: 14,
                      fontFamily: 'Kanit',
                    ),
                    indicatorSize: TabBarIndicatorSize.tab,
                    dividerColor: Colors.transparent,
                    tabs: const [
                      Tab(text: 'ประวัติการลา'),
                      Tab(text: 'ประวัติเปลี่ยนเวร'),
                    ],
                  ),
                ),

                // Tab Views
                Expanded(
                  child: TabBarView(
                    controller: _tabController,
                    physics: const BouncingScrollPhysics(),
                    children: [
                      _buildLeaveHistory(leaveProvider),
                      _buildGuardHistory(guardProvider),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          if (_tabController.index == 0) {
            Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const LeaveRequestScreen()),
            ).then((_) => _refreshData());
          } else {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => const GuardChangeRequestScreen(),
              ),
            ).then((_) => _refreshData());
          }
        },
        backgroundColor: const Color(0xFF3B82F6),
        child: const Icon(Icons.add, color: Colors.white, size: 28),
        elevation: 4,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      ),
    );
  }

  Widget _buildLeaveHistory(LeaveProvider provider) {
    if (provider.isLoading && provider.myRequests.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    // Filter requests
    final filteredRequests = provider.myRequests.where((request) {
      if (_leaveFilter == 'all') return true;
      if (_leaveFilter == 'pending') {
        return request.status == 'pending' ||
            request.status == 'waiting_head' ||
            request.status == 'waiting_hr' ||
            request.status == 'waiting_director';
      }
      return request.status == _leaveFilter;
    }).toList();

    return Column(
      children: [
        // Filter Tabs
        Container(
          height: 60,
          padding: const EdgeInsets.symmetric(vertical: 12),
          child: ListView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 24),
            children: [
              _buildFilterChip('ทั้งหมด', 'all'),
              const SizedBox(width: 8),
              _buildFilterChip('รออนุมัติ', 'pending'),
              const SizedBox(width: 8),
              _buildFilterChip('อนุมัติแล้ว', 'approved'),
              const SizedBox(width: 8),
              _buildFilterChip('ปฏิเสธ', 'rejected'),
            ],
          ),
        ),

        // List
        Expanded(
          child: filteredRequests.isEmpty
              ? _buildEmptyState(
                  Icons.event_busy_rounded,
                  'ไม่พบข้อมูลการลา',
                  'ไม่มีรายการในช่วงที่เลือก',
                )
              : RefreshIndicator(
                  onRefresh: () async {
                    HapticFeedback.mediumImpact();
                    await provider.fetchMyRequests();
                  },
                  displacement: 20,
                  color: AppTheme.primary,
                  backgroundColor: Colors.white,
                  child: ListView.separated(
                    physics: const BouncingScrollPhysics(
                      parent: AlwaysScrollableScrollPhysics(),
                    ),
                    padding: const EdgeInsets.fromLTRB(24, 8, 24, 100),
                    itemCount: filteredRequests.length,
                    separatorBuilder: (context, index) =>
                        const SizedBox(height: 16),
                    itemBuilder: (context, index) {
                      final request = filteredRequests[index];
                      return _buildAnimatedListItem(
                        index,
                        _buildLeaveCard(request),
                      );
                    },
                  ),
                ),
        ),
      ],
    );
  }

  Widget _buildFilterChip(String label, String value) {
    final isSelected = _leaveFilter == value;
    return GestureDetector(
      onTap: () {
        setState(() {
          _leaveFilter = value;
        });
        HapticFeedback.lightImpact();
      },
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
        decoration: BoxDecoration(
          color: isSelected ? Colors.white : const Color(0xFFF1F5F9),
          borderRadius: BorderRadius.circular(12),
          boxShadow: isSelected
              ? [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05),
                    blurRadius: 4,
                    offset: const Offset(0, 2),
                  ),
                ]
              : [],
        ),
        child: Center(
          child: Text(
            label,
            style: GoogleFonts.kanit(
              fontSize: 14,
              fontWeight: isSelected ? FontWeight.w600 : FontWeight.w400,
              color: isSelected
                  ? const Color(0xFF1E293B)
                  : const Color(0xFF64748B),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildGuardHistory(GuardChangeProvider provider) {
    if (provider.isLoading && provider.myRequests.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    final filteredRequests = provider.myRequests.where((request) {
      if (_guardFilter == 'all') return true;
      if (_guardFilter == 'pending') {
        return [
          'pending',
          'approved',
          'director_approved',
        ].contains(request.status);
      }
      if (_guardFilter == 'completed')
        return request.status == 'fully_approved';
      if (_guardFilter == 'cancelled') {
        return ['rejected', 'cancelled'].contains(request.status);
      }
      return false;
    }).toList();

    return Column(
      children: [
        // Filter Tabs
        Container(
          height: 60,
          padding: const EdgeInsets.symmetric(vertical: 12),
          child: ListView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 24),
            children: [
              _buildGuardFilterChip('ทั้งหมด', 'all'),
              const SizedBox(width: 8),
              _buildGuardFilterChip('รออนุมัติ', 'pending'),
              const SizedBox(width: 8),
              _buildGuardFilterChip('อนุมัติแล้ว', 'completed'),
              const SizedBox(width: 8),
              _buildGuardFilterChip('ยกเลิก', 'cancelled'),
            ],
          ),
        ),

        // List
        Expanded(
          child: filteredRequests.isEmpty
              ? _buildEmptyState(
                  Icons.security_rounded,
                  'ไม่พบประวัติเปลี่ยนเวร',
                  'ไม่มีรายการในช่วงที่เลือก',
                )
              : RefreshIndicator(
                  onRefresh: () async {
                    HapticFeedback.mediumImpact();
                    await provider.fetchMyRequests();
                  },
                  displacement: 20,
                  color: AppTheme.primary,
                  backgroundColor: Colors.white,
                  child: ListView.separated(
                    physics: const BouncingScrollPhysics(
                      parent: AlwaysScrollableScrollPhysics(),
                    ),
                    padding: const EdgeInsets.fromLTRB(24, 8, 24, 100),
                    itemCount: filteredRequests.length,
                    separatorBuilder: (context, index) =>
                        const SizedBox(height: 16),
                    itemBuilder: (context, index) {
                      final request = filteredRequests[index];
                      return _buildAnimatedListItem(
                        index,
                        _buildGuardCard(request),
                      );
                    },
                  ),
                ),
        ),
      ],
    );
  }

  Widget _buildGuardFilterChip(String label, String value) {
    final isSelected = _guardFilter == value;
    return GestureDetector(
      onTap: () {
        setState(() {
          _guardFilter = value;
        });
        HapticFeedback.lightImpact();
      },
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
        decoration: BoxDecoration(
          color: isSelected ? Colors.white : const Color(0xFFF1F5F9),
          borderRadius: BorderRadius.circular(12),
          boxShadow: isSelected
              ? [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05),
                    blurRadius: 4,
                    offset: const Offset(0, 2),
                  ),
                ]
              : [],
        ),
        child: Center(
          child: Text(
            label,
            style: GoogleFonts.kanit(
              fontSize: 14,
              fontWeight: isSelected ? FontWeight.w600 : FontWeight.w400,
              color: isSelected
                  ? const Color(0xFF1E293B)
                  : const Color(0xFF64748B),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildAnimatedListItem(int index, Widget child) {
    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0.0, end: 1.0),
      duration: Duration(milliseconds: 400 + (index * 100)),
      curve: Curves.easeOutCubic,
      builder: (context, value, child) {
        return Transform.translate(
          offset: Offset(0, 30 * (1 - value)),
          child: Opacity(opacity: value, child: child),
        );
      },
      child: child,
    );
  }

  Widget _buildEmptyState(IconData icon, String title, String subtitle) {
    return Center(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: Colors.white,
              shape: BoxShape.circle,
              boxShadow: [
                BoxShadow(
                  color: Colors.indigo.withOpacity(0.1),
                  blurRadius: 30,
                  offset: const Offset(0, 10),
                ),
              ],
            ),
            child: Icon(icon, size: 48, color: const Color(0xFF6366F1)),
          ),
          const SizedBox(height: 24),
          Text(
            title,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w800,
              color: Color(0xFF1E293B),
            ),
          ),
          const SizedBox(height: 8),
          Text(
            subtitle,
            style: const TextStyle(fontSize: 14, color: Color(0xFF64748B)),
          ),
        ],
      ),
    );
  }

  Widget _buildLeaveCard(LeaveRequest request) {
    final statusColor = _getLeaveStatusColor(request.status);
    final statusIcon = _getLeaveStatusIcon(request.status);
    final leaveIcon = _getLeaveTypeIcon(request.leaveType.slug);
    final leaveColor = _getLeaveTypeColor(request.leaveType.slug);

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            // Top Row: Icon + Title + Status
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Icon
                Container(
                  width: 48,
                  height: 48,
                  decoration: BoxDecoration(
                    color: leaveColor.withOpacity(0.1),
                    shape: BoxShape.circle,
                  ),
                  child: Icon(leaveIcon, color: leaveColor, size: 24),
                ),
                const SizedBox(width: 16),

                // Title & Duration
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        request.leaveType.name,
                        style: GoogleFonts.kanit(
                          fontSize: 16,
                          fontWeight: FontWeight.w700,
                          color: const Color(0xFF1E293B),
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        request.reason.isNotEmpty
                            ? request.reason
                            : 'ไม่มีเหตุผลระบุ',
                        style: GoogleFonts.sarabun(
                          fontSize: 13,
                          color: const Color(0xFF94A3B8),
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),

                // Status Badge
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 10,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: statusColor.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(statusIcon, size: 14, color: statusColor),
                      const SizedBox(width: 4),
                      Text(
                        _getLeaveStatusSimpleText(request.status).toUpperCase(),
                        style: GoogleFonts.kanit(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: statusColor,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            const Divider(height: 1, color: Color(0xFFF1F5F9)),
            const SizedBox(height: 16),

            // Bottom Row: Date & Days
            Row(
              children: [
                Icon(
                  Icons.calendar_today_rounded,
                  size: 16,
                  color: const Color(0xFF64748B),
                ),
                const SizedBox(width: 8),
                Text(
                  _formatDateRange(request.startDate, request.endDate),
                  style: GoogleFonts.sarabun(
                    fontSize: 14,
                    fontWeight: FontWeight.w500,
                    color: const Color(0xFF475569),
                  ),
                ),
                const Spacer(),
                Text(
                  '${request.totalDays} วัน',
                  style: GoogleFonts.kanit(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                    color: const Color(0xFF1E293B),
                  ),
                ),
              ],
            ),
            if (request.status == 'approved') ...[
              const SizedBox(height: 16),
              const Divider(height: 1, color: Color(0xFFF1F5F9)),
              InkWell(
                onTap: () {
                  HapticFeedback.lightImpact();
                  _downloadLeavePdf(request.id);
                },
                borderRadius: const BorderRadius.vertical(
                  bottom: Radius.circular(20),
                ),
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(vertical: 12),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(
                        Icons.download_rounded,
                        size: 18,
                        color: Color(0xFF3B82F6),
                      ),
                      const SizedBox(width: 8),
                      Text(
                        'ดาวน์โหลดใบลา',
                        style: GoogleFonts.kanit(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: const Color(0xFF3B82F6),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  String _formatDateRange(DateTime start, DateTime end) {
    final startStr = "${_mToMonth(start.month)} ${start.day}"; // e.g. Oct 24
    if (start.year == end.year &&
        start.month == end.month &&
        start.day == end.day) {
      return startStr;
    }
    final endStr = "${_mToMonth(end.month)} ${end.day}";
    return "$startStr - $endStr";
  }

  String _mToMonth(int m) {
    const months = [
      "ม.ค.",
      "ก.พ.",
      "มี.ค.",
      "เม.ย.",
      "พ.ค.",
      "มิ.ย.",
      "ก.ค.",
      "ส.ค.",
      "ก.ย.",
      "ต.ค.",
      "พ.ย.",
      "ธ.ค.",
    ];
    return months[m - 1];
  }

  Widget _buildGuardCard(GuardChangeRequest request) {
    final statusColor = _getGuardStatusColor(request.status);
    final statusLabel = _getGuardStatusText(request.status);
    final isCompleted = request.status == 'fully_approved';
    final isCancelled = ['rejected', 'cancelled'].contains(request.status);

    DateTime? createdDate;
    try {
      createdDate = DateTime.parse(request.createdAt);
    } catch (_) {}

    final dateHeader = createdDate != null
        ? "${createdDate.day} ${_mToMonth(createdDate.month)}".toUpperCase()
        : "";

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 15,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        children: [
          // Header: Date & Status
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 20, 20, 0),
            child: Row(
              children: [
                Text(
                  (isCompleted
                          ? 'เสร็จสิ้น $dateHeader'
                          : isCancelled
                          ? 'ยกเลิก $dateHeader'
                          : 'ยื่นคำขอ $dateHeader')
                      .toUpperCase(),
                  style: GoogleFonts.kanit(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: const Color(0xFF94A3B8),
                    letterSpacing: 1.2,
                  ),
                ),
                const Spacer(),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 6,
                  ),
                  decoration: BoxDecoration(
                    color: statusColor.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Container(
                        width: 6,
                        height: 6,
                        decoration: BoxDecoration(
                          color: statusColor,
                          shape: BoxShape.circle,
                        ),
                      ),
                      const SizedBox(width: 6),
                      Text(
                        statusLabel,
                        style: GoogleFonts.kanit(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: statusColor,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 20),

          // Timeline Content
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Stack(
              children: [
                // Connector Line (Background)
                Positioned(
                  left: 23, // Center of width 48 (24) - half line width (1)
                  top: 36, // Center of top item (72/2)
                  bottom: 36, // Center of bottom item (72/2)
                  child: Container(width: 2, color: const Color(0xFFE2E8F0)),
                ),

                // Content
                Column(
                  children: [
                    // Top Item
                    SizedBox(
                      height: 72,
                      child: Row(
                        children: [
                          // Avatar
                          SizedBox(
                            width: 48,
                            child: Center(
                              child: _buildAvatar(request.user?.avatarUrl),
                            ),
                          ),
                          const SizedBox(width: 16),
                          // Text
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Text(
                                  'MY SHIFT',
                                  style: GoogleFonts.kanit(
                                    fontSize: 10,
                                    fontWeight: FontWeight.w700,
                                    color: const Color(0xFF94A3B8),
                                    letterSpacing: 0.5,
                                  ),
                                ),
                                Text(
                                  request.dutyPositionThai,
                                  style: GoogleFonts.kanit(
                                    fontSize: 15,
                                    fontWeight: FontWeight.w600,
                                    color: const Color(0xFF1E293B),
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                Text(
                                  request.formattedDutyDate,
                                  style: GoogleFonts.sarabun(
                                    fontSize: 13,
                                    color: const Color(0xFF64748B),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),

                    // Spacer / Icon
                    Container(
                      height: 32, // Gap between items
                      alignment: Alignment.centerLeft,
                      child: SizedBox(
                        width: 48,
                        child: Center(
                          child: Container(
                            width: 24,
                            height: 24,
                            decoration: BoxDecoration(
                              color: Colors.white,
                              shape: BoxShape.circle,
                              border: Border.all(
                                color: const Color(0xFFE2E8F0),
                              ),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(0.03),
                                  blurRadius: 4,
                                  offset: const Offset(0, 2),
                                ),
                              ],
                            ),
                            child: const Icon(
                              Icons.arrow_downward_rounded,
                              size: 14,
                              color: Color(0xFF64748B),
                            ),
                          ),
                        ),
                      ),
                    ),

                    // Bottom Item
                    SizedBox(
                      height: 72,
                      child: Row(
                        children: [
                          // Avatar
                          SizedBox(
                            width: 48,
                            child: Center(
                              child: _buildAvatar(
                                request.replacementUser?.avatarUrl,
                              ),
                            ),
                          ),
                          const SizedBox(width: 16),
                          // Text
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Text(
                                  (request.replacementUser?.fullName ?? '-')
                                      .toUpperCase(),
                                  style: GoogleFonts.kanit(
                                    fontSize: 10,
                                    fontWeight: FontWeight.w700,
                                    color: const Color(0xFF94A3B8),
                                    letterSpacing: 0.5,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                Text(
                                  request.dutyPositionThai,
                                  style: GoogleFonts.kanit(
                                    fontSize: 15,
                                    fontWeight: FontWeight.w600,
                                    color: const Color(0xFF1E293B),
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                Text(
                                  request.formattedDutyDate,
                                  style: GoogleFonts.sarabun(
                                    fontSize: 13,
                                    color: const Color(0xFF64748B),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 24),
          if (isCompleted) ...[
            const Divider(height: 1, color: Color(0xFFF1F5F9)),
            _buildDownloadButton(request.id),
          ],
          const SizedBox(height: 8),
        ],
      ),
    );
  }

  Widget _buildAvatar(String? url) {
    return Container(
      width: 48,
      height: 48,
      decoration: BoxDecoration(
        color: const Color(0xFFF1F5F9),
        shape: BoxShape.circle,
        border: Border.all(color: Colors.white, width: 2),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
        image: url != null
            ? DecorationImage(image: NetworkImage(url), fit: BoxFit.cover)
            : null,
      ),
      child: url == null
          ? const Icon(Icons.person_rounded, color: Color(0xFF94A3B8))
          : null,
    );
  }

  Widget _buildDownloadButton(int id) {
    return InkWell(
      onTap: () {
        HapticFeedback.lightImpact();
        _downloadGuardPdf(id);
      },
      borderRadius: const BorderRadius.vertical(bottom: Radius.circular(24)),
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(vertical: 12),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(
              Icons.download_rounded,
              size: 18,
              color: Color(0xFF3B82F6),
            ),
            const SizedBox(width: 8),
            Text(
              'ดาวน์โหลดรายงาน',
              style: GoogleFonts.kanit(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: const Color(0xFF3B82F6),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // --- Helpers ---
  Color _getLeaveStatusColor(String status) {
    switch (status) {
      case 'approved':
        return const Color(0xFF10B981);
      case 'rejected':
        return const Color(0xFFEF4444);
      default:
        return const Color(0xFFF59E0B);
    }
  }

  Color _getLeaveTypeColor(String slug) {
    switch (slug) {
      case 'vacation':
        return const Color(0xFF3B82F6); // Blue
      case 'sick':
        return const Color(0xFFF97316); // Orange
      case 'personal':
        return const Color(0xFF8B5CF6); // Purple
      default:
        return const Color(0xFF64748B); // Slate
    }
  }

  IconData _getLeaveTypeIcon(String slug) {
    switch (slug) {
      case 'vacation':
        return Icons
            .umbrella_rounded; // Assuming this fits "Annual Leave" better
      case 'sick':
        return Icons.sick_rounded;
      case 'personal':
        return Icons.person_rounded;
      default:
        return Icons.work_off_rounded;
    }
  }

  // Simplified status text for the badge
  String _getLeaveStatusSimpleText(String status) {
    switch (status) {
      case 'approved':
        return 'อนุมัติ';
      case 'rejected':
        return 'ปฏิเสธ';
      default:
        return 'รออนุมัติ';
    }
  }

  IconData _getLeaveStatusIcon(String status) {
    switch (status) {
      case 'approved':
        return Icons.check_circle_rounded;
      case 'rejected':
        return Icons.cancel_rounded;
      default:
        return Icons.access_time_filled_rounded;
    }
  }

  Color _getGuardStatusColor(String status) {
    if (status == 'fully_approved') return const Color(0xFF10B981);
    if (status == 'rejected' || status == 'cancelled') {
      return const Color(0xFFEF4444);
    }
    if (status == 'pending') return const Color(0xFFF59E0B);
    return const Color(0xFF3B82F6);
  }

  String _getGuardStatusText(String status) {
    if (status == 'fully_approved') return 'อนุมัติแล้ว';
    if (status == 'rejected') return 'ปฏิเสธ';
    if (status == 'cancelled') return 'ยกเลิก';
    if (status == 'pending') return 'รอผู้แทน';
    if (status == 'approved') return 'รอหัวหน้า';
    if (status == 'director_approved') return 'รอ ผอ.';
    return 'รออนุมัติ';
  }

  Future<void> _downloadGuardPdf(int id) async {
    try {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text(
            'กำลังดาวน์โหลดรายงาน PDF...',
            style: TextStyle(fontFamily: 'NotoSansThai'),
          ),
          backgroundColor: const Color(0xFF1E293B),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(10),
          ),
          margin: const EdgeInsets.all(16),
        ),
      );
      await PdfService.downloadAndOpenGuardChangePdf(id, 'GuardChange_$id');
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('ไม่สามารถเปิดไฟล์ PDF ได้'),
            backgroundColor: AppTheme.error,
            behavior: SnackBarBehavior.floating,
            margin: const EdgeInsets.all(16),
          ),
        );
      }
    }
  }

  Future<void> _downloadLeavePdf(int id) async {
    try {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text(
            'กำลังดาวน์โหลดใบลา PDF...',
            style: TextStyle(fontFamily: 'NotoSansThai'),
          ),
          backgroundColor: const Color(0xFF1E293B),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(10),
          ),
          margin: const EdgeInsets.all(16),
        ),
      );
      await PdfService.downloadAndOpenPdf(id, 'LeaveRequest_$id');
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('ไม่สามารถเปิดไฟล์ PDF ได้'),
            backgroundColor: AppTheme.error,
            behavior: SnackBarBehavior.floating,
            margin: const EdgeInsets.all(16),
          ),
        );
      }
    }
  }
}
