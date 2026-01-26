import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../models/leave_request_model.dart';
import '../models/guard_change_model.dart';
import '../providers/leave_provider.dart';
import '../providers/guard_change_provider.dart';
import '../services/pdf_service.dart';

class ActivityScreen extends StatefulWidget {
  const ActivityScreen({super.key});

  @override
  State<ActivityScreen> createState() => _ActivityScreenState();
}

class _ActivityScreenState extends State<ActivityScreen>
    with TickerProviderStateMixin {
  late TabController _tabController;
  late AnimationController _animationController;

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
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: Colors.white,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text(
          'รายการกิจกรรม',
          style: TextStyle(fontWeight: FontWeight.w900),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: AppTheme.textMain,
        elevation: 0,
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(70),
          child: Container(
            margin: const EdgeInsets.symmetric(horizontal: 28),
            decoration: BoxDecoration(
              color: Colors.black.withOpacity(0.04),
              borderRadius: BorderRadius.circular(20),
            ),
            child: TabBar(
              controller: _tabController,
              indicator: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05),
                    blurRadius: 10,
                  ),
                ],
              ),
              labelColor: AppTheme.primary,
              unselectedLabelColor: AppTheme.textSub,
              labelStyle: const TextStyle(
                fontWeight: FontWeight.w900,
                fontSize: 13,
              ),
              indicatorSize: TabBarIndicatorSize.tab,
              dividerColor: Colors.transparent,
              tabs: const [
                Tab(text: 'ประวัติการลา'),
                Tab(text: 'ประวัติเปลี่ยนเวร'),
              ],
            ),
          ),
        ),
      ),
      body: Stack(
        children: [
          Container(
            height: size.height,
            width: size.width,
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [Color(0xFFF8FAFC), Colors.white, Color(0xFFF0F7FF)],
              ),
            ),
          ),
          _buildFloatingCircle(
            top: -40,
            right: -40,
            size: 220,
            color: AppTheme.primary.withOpacity(0.06),
          ),
          _buildFloatingCircle(
            bottom: -50,
            left: -50,
            size: 280,
            color: AppTheme.secondary.withOpacity(0.05),
          ),

          SafeArea(
            child: FadeTransition(
              opacity: _animationController,
              child: TabBarView(
                controller: _tabController,
                physics: const BouncingScrollPhysics(),
                children: [
                  _buildLeaveHistory(leaveProvider),
                  _buildGuardHistory(guardProvider),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFloatingCircle({
    required double size,
    required Color color,
    double? top,
    double? bottom,
    double? left,
    double? right,
  }) {
    return Positioned(
      top: top,
      bottom: bottom,
      left: left,
      right: right,
      child: Container(
        width: size,
        height: size,
        decoration: BoxDecoration(shape: BoxShape.circle, color: color),
      ),
    );
  }

  Widget _buildEmptyState(IconData icon, String title, String subtitle) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(32),
            decoration: BoxDecoration(
              color: AppTheme.border.withOpacity(0.2),
              shape: BoxShape.circle,
            ),
            child: Icon(
              icon,
              size: 64,
              color: AppTheme.textSub.withOpacity(0.2),
            ),
          ),
          const SizedBox(height: 24),
          Text(
            title,
            style: const TextStyle(
              color: AppTheme.textSub,
              fontSize: 18,
              fontWeight: FontWeight.w700,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            subtitle,
            style: TextStyle(
              color: AppTheme.textSub.withOpacity(0.5),
              fontSize: 14,
            ),
          ),
        ],
      ),
    );
  }

  // --- Leave History Implementation ---

  Widget _buildLeaveHistory(LeaveProvider provider) {
    if (provider.isLoading && provider.myRequests.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    if (provider.myRequests.isEmpty) {
      return _buildEmptyState(
        Icons.history_toggle_off_rounded,
        'ยังไม่มีประวัติการลา',
        'รายการที่คุณยื่นลาจะปรากฏที่นี่',
      );
    }

    return RefreshIndicator(
      onRefresh: () async => provider.fetchMyRequests(),
      child: ListView.builder(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.only(top: 20, bottom: 120),
        itemCount: provider.myRequests.length,
        itemBuilder: (context, index) =>
            _buildLeaveCard(provider.myRequests[index]),
      ),
    );
  }

  Widget _buildLeaveCard(LeaveRequest request) {
    final statusColor = _getLeaveStatusColor(request.status);
    final statusIcon = _getLeaveStatusIcon(request.status);

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 24, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(32),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 5, sigmaY: 5),
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      width: 56,
                      height: 56,
                      decoration: BoxDecoration(
                        color: statusColor.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Icon(
                        _getLeaveTypeIcon(request.leaveType.slug),
                        color: statusColor,
                        size: 26,
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            request.leaveType.name,
                            style: const TextStyle(
                              fontWeight: FontWeight.w900,
                              fontSize: 17,
                              color: AppTheme.textMain,
                            ),
                          ),
                          Text(
                            'ยื่นเมื่อ ${request.formattedCreatedAt}',
                            style: const TextStyle(
                              color: AppTheme.textSub,
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ],
                      ),
                    ),
                    _buildStatusChip(
                      _getLeaveStatusText(request.status),
                      statusColor,
                      statusIcon,
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Color(0xFFF1F5F9),
                    borderRadius: BorderRadius.circular(24),
                  ),
                  child: Row(
                    children: [
                      _buildInfoItem(
                        'ระยะเวลา',
                        '${request.formattedStartDate} - ${request.formattedEndDate}',
                        Icons.calendar_today_rounded,
                      ),
                      Container(
                        width: 1,
                        height: 30,
                        color: AppTheme.border,
                        margin: const EdgeInsets.symmetric(horizontal: 16),
                      ),
                      _buildInfoItem(
                        'จำนวน',
                        '${request.totalDays} วัน',
                        Icons.timer_outlined,
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: [
                    TextButton.icon(
                      onPressed: () => _downloadLeavePdf(request),
                      icon: const Icon(Icons.picture_as_pdf_rounded, size: 18),
                      label: const Text(
                        'PDF',
                        style: TextStyle(
                          fontWeight: FontWeight.w800,
                          fontSize: 13,
                        ),
                      ),
                      style: TextButton.styleFrom(
                        foregroundColor: AppTheme.primary,
                        backgroundColor: AppTheme.primary.withOpacity(0.08),
                        padding: const EdgeInsets.symmetric(
                          horizontal: 16,
                          vertical: 10,
                        ),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14),
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // --- Guard History Implementation ---

  Widget _buildGuardHistory(GuardChangeProvider provider) {
    if (provider.isLoading && provider.myRequests.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    if (provider.myRequests.isEmpty) {
      return _buildEmptyState(
        Icons.history_toggle_off_rounded,
        'ยังไม่มีประวัติการเปลี่ยนเวร',
        'รายการที่คุณขอเปลี่ยนเวรยามจะปรากฏที่นี่',
      );
    }

    return RefreshIndicator(
      onRefresh: () async => provider.fetchMyRequests(),
      child: ListView.builder(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.only(top: 20, bottom: 120),
        itemCount: provider.myRequests.length,
        itemBuilder: (context, index) =>
            _buildGuardCard(provider.myRequests[index]),
      ),
    );
  }

  Widget _buildGuardCard(GuardChangeRequest request) {
    final statusColor = _getGuardStatusColor(request.status);

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 24, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(32),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 5, sigmaY: 5),
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      width: 56,
                      height: 56,
                      decoration: BoxDecoration(
                        color: statusColor.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: const Icon(
                        Icons.security_rounded,
                        color: Colors.blueAccent,
                        size: 26,
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            request.dutyPositionThai,
                            style: const TextStyle(
                              fontWeight: FontWeight.w900,
                              fontSize: 17,
                              color: AppTheme.textMain,
                            ),
                          ),
                          Text(
                            'ยื่นเมื่อ ${request.formattedCreatedAt}',
                            style: const TextStyle(
                              color: AppTheme.textSub,
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ],
                      ),
                    ),
                    _buildStatusChip(
                      _getGuardStatusText(request.status),
                      statusColor,
                      Icons.pending_rounded,
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Color(0xFFF1F5F9),
                    borderRadius: BorderRadius.circular(24),
                  ),
                  child: Row(
                    children: [
                      _buildInfoItem(
                        'วันที่ปฏิบัติเวร',
                        request.formattedDutyDate,
                        Icons.calendar_month_outlined,
                      ),
                      Container(
                        width: 1,
                        height: 30,
                        color: AppTheme.border,
                        margin: const EdgeInsets.symmetric(horizontal: 16),
                      ),
                      _buildInfoItem(
                        'ผู้มาเปลี่ยนแทน',
                        request.replacementUser?.name ?? '-',
                        Icons.person_outline,
                      ),
                    ],
                  ),
                ),
                if (request.status == 'fully_approved') ...[
                  const SizedBox(height: 20),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      TextButton.icon(
                        onPressed: () => _downloadGuardPdf(request.id),
                        icon: const Icon(
                          Icons.picture_as_pdf_rounded,
                          size: 18,
                        ),
                        label: const Text(
                          'PDF',
                          style: TextStyle(
                            fontWeight: FontWeight.w800,
                            fontSize: 13,
                          ),
                        ),
                        style: TextButton.styleFrom(
                          foregroundColor: AppTheme.primary,
                          backgroundColor: AppTheme.primary.withOpacity(0.08),
                          padding: const EdgeInsets.symmetric(
                            horizontal: 16,
                            vertical: 10,
                          ),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14),
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          ),
        ),
      ),
    );
  }

  // --- Helpers ---

  Widget _buildStatusChip(String text, Color color, IconData icon) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: color.withOpacity(0.12),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 12, color: color),
          const SizedBox(width: 6),
          Text(
            text,
            style: TextStyle(
              color: color,
              fontWeight: FontWeight.w900,
              fontSize: 11,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoItem(String label, String value, IconData icon) {
    return Expanded(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, size: 12, color: AppTheme.textSub),
              const SizedBox(width: 4),
              Text(
                label,
                style: const TextStyle(
                  color: AppTheme.textSub,
                  fontSize: 11,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ],
          ),
          const SizedBox(height: 4),
          Text(
            value,
            style: const TextStyle(
              color: AppTheme.textMain,
              fontSize: 13,
              fontWeight: FontWeight.w800,
            ),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }

  Color _getLeaveStatusColor(String status) => status == 'approved'
      ? AppTheme.success
      : (status == 'rejected' ? AppTheme.error : AppTheme.warning);
  IconData _getLeaveStatusIcon(String status) => status == 'approved'
      ? Icons.check_circle_rounded
      : (status == 'rejected' ? Icons.cancel_rounded : Icons.pending_rounded);
  String _getLeaveStatusText(String status) => status == 'approved'
      ? 'อนุมัติ'
      : (status == 'rejected' ? 'ปฏิเสธ' : 'รออนุมัติ');
  IconData _getLeaveTypeIcon(String slug) => slug == 'vacation'
      ? Icons.beach_access_rounded
      : (slug == 'sick'
            ? Icons.medication_rounded
            : Icons.business_center_rounded);

  Color _getGuardStatusColor(String status) {
    if (status == 'fully_approved') return AppTheme.success;
    if (status == 'rejected' || status == 'cancelled') return AppTheme.error;
    if (status == 'pending') return AppTheme.warning;
    return Colors.blue;
  }

  String _getGuardStatusText(String status) {
    if (status == 'fully_approved') return 'อนุมัติเรียบร้อย';
    if (status == 'rejected') return 'ปฏิเสธ';
    if (status == 'cancelled') return 'ยกเลิกแล้ว';
    if (status == 'pending') return 'รอผู้แทนยืนยัน';
    if (status == 'approved') return 'รอหัวหน้าแผนก';
    if (status == 'director_approved') return 'รอ ผอ. อนุมัติ';
    return 'กำลังรอ';
  }

  Future<void> _downloadLeavePdf(LeaveRequest request) async {
    final fileName = "Leave_${request.leaveType.slug}_${request.id}";
    await PdfService.downloadAndOpenPdf(request.id, fileName);
  }

  Future<void> _downloadGuardPdf(int id) async {
    await PdfService.downloadAndOpenGuardChangePdf(id, 'GuardChange_$id');
  }
}
