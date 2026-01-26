import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../models/guard_change_model.dart';
import '../providers/guard_change_provider.dart';
import '../services/pdf_service.dart';

class GuardChangeHistoryScreen extends StatefulWidget {
  const GuardChangeHistoryScreen({super.key});

  @override
  State<GuardChangeHistoryScreen> createState() =>
      _GuardChangeHistoryScreenState();
}

class _GuardChangeHistoryScreenState extends State<GuardChangeHistoryScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _animationController;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1000),
    )..forward();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<GuardChangeProvider>(
        context,
        listen: false,
      ).fetchMyRequests();
    });
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<GuardChangeProvider>(context);
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: Colors.white,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text(
          'ประวัติการเปลี่ยนยาม',
          style: TextStyle(fontWeight: FontWeight.w900),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: AppTheme.textMain,
        elevation: 0,
      ),
      body: Stack(
        children: [
          // Background Design
          Container(
            height: size.height,
            width: size.width,
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [Color(0xFFF8FAFC), Colors.white, Color(0xFFF0F9FF)],
              ),
            ),
          ),
          _buildFloatingCircle(
            top: -40,
            right: -40,
            size: 220,
            color: Colors.blue.withOpacity(0.06),
          ),
          _buildFloatingCircle(
            bottom: -50,
            left: -50,
            size: 280,
            color: Colors.teal.withOpacity(0.05),
          ),

          SafeArea(
            child: RefreshIndicator(
              onRefresh: () => provider.fetchMyRequests(),
              color: AppTheme.primary,
              child: FadeTransition(
                opacity: _animationController,
                child: _buildContent(provider),
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

  Widget _buildContent(GuardChangeProvider provider) {
    if (provider.isLoading && provider.myRequests.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    if (provider.myRequests.isEmpty) {
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
                Icons.history_toggle_off_rounded,
                size: 64,
                color: AppTheme.textSub.withOpacity(0.2),
              ),
            ),
            const SizedBox(height: 24),
            const Text(
              'ยังไม่มีประวัติการเปลี่ยนยาม',
              style: TextStyle(
                color: AppTheme.textSub,
                fontSize: 18,
                fontWeight: FontWeight.w700,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'รายการที่คุณขอเปลี่ยนเวรยามจะปรากฏที่นี่',
              style: TextStyle(
                color: AppTheme.textSub.withOpacity(0.5),
                fontSize: 14,
              ),
            ),
          ],
        ),
      );
    }

    return CustomScrollView(
      physics: const BouncingScrollPhysics(),
      slivers: [
        SliverPadding(
          padding: const EdgeInsets.fromLTRB(28, 16, 28, 16),
          sliver: SliverToBoxAdapter(
            child: Text(
              'รายการทั้งหมด (${provider.myRequests.length})',
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w900,
                color: AppTheme.textMain,
              ),
            ),
          ),
        ),
        SliverList(
          delegate: SliverChildBuilderDelegate((context, index) {
            final request = provider.myRequests[index];
            return _buildHistoryCard(request);
          }, childCount: provider.myRequests.length),
        ),
        const SliverPadding(padding: EdgeInsets.only(bottom: 100)),
      ],
    );
  }

  Widget _buildHistoryCard(GuardChangeRequest request) {
    final statusColor = _getStatusColor(request.status);

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
                    _buildStatusChip(request.status, statusColor),
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
                        onPressed: () => _openPdf(request.id),
                        icon: const Icon(
                          Icons.picture_as_pdf_rounded,
                          size: 18,
                        ),
                        label: const Text(
                          'ดาวน์โหลดรายงาน',
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

  Widget _buildStatusChip(String status, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: color.withOpacity(0.12),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Text(
        _getStatusText(status),
        style: TextStyle(
          color: color,
          fontWeight: FontWeight.w900,
          fontSize: 11,
        ),
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

  Color _getStatusColor(String status) {
    switch (status) {
      case 'fully_approved':
        return AppTheme.success;
      case 'rejected':
      case 'cancelled':
        return AppTheme.error;
      case 'pending':
        return AppTheme.warning;
      case 'approved':
      case 'director_approved':
        return Colors.blue;
      default:
        return AppTheme.primary;
    }
  }

  String _getStatusText(String status) {
    switch (status) {
      case 'fully_approved':
        return 'อนุมัติเรียบร้อย';
      case 'rejected':
        return 'ปฏิเสธ';
      case 'cancelled':
        return 'ยกเลิกแล้ว';
      case 'pending':
        return 'รอผู้แทนยืนยัน';
      case 'approved':
        return 'รอหัวหน้าแผนก';
      case 'director_approved':
        return 'รอ ผอ. อนุมัติ';
      default:
        return 'กำลังรอ';
    }
  }

  Future<void> _openPdf(int id) async {
    try {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text(
            'กำลังดาวน์โหลดรายงาน PDF...',
            style: TextStyle(fontWeight: FontWeight.w600),
          ),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
        ),
      );
      await PdfService.downloadAndOpenGuardChangePdf(id, 'GuardChange_$id');
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('ไม่สามารถดาวน์โหลด PDF: $e'),
            backgroundColor: AppTheme.error,
            behavior: SnackBarBehavior.floating,
          ),
        );
      }
    }
  }
}
