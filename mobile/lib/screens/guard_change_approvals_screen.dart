import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../models/guard_change_model.dart';
import '../providers/guard_change_provider.dart';
import '../providers/auth_provider.dart';
import '../widgets/signature_dialog.dart';
import '../widgets/animated_background.dart';

class GuardChangeApprovalsScreen extends StatefulWidget {
  const GuardChangeApprovalsScreen({super.key});

  @override
  State<GuardChangeApprovalsScreen> createState() =>
      _GuardChangeApprovalsScreenState();
}

class _GuardChangeApprovalsScreenState extends State<GuardChangeApprovalsScreen>
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
      Provider.of<GuardChangeProvider>(context, listen: false).fetchApprovals();
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

    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text(
          'คำขอถึงฉัน',
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
          const Positioned.fill(child: AnimatedBackground()),
          SafeArea(
            child: RefreshIndicator(
              onRefresh: () async {
                HapticFeedback.mediumImpact();
                await provider.fetchApprovals();
              },
              color: AppTheme.primary,
              backgroundColor: Colors.white,
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

  Widget _buildContent(GuardChangeProvider provider) {
    if (provider.isLoading && provider.approvals.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    if (provider.approvals.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
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
              child: const Icon(
                Icons.mark_email_read_rounded,
                size: 48,
                color: Color(0xFF6366F1),
              ),
            ),
            const SizedBox(height: 24),
            const Text(
              'ไม่มีคำขอรอยืนยัน',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w800,
                color: Color(0xFF1E293B),
              ),
            ),
            const SizedBox(height: 8),
            const Text(
              'เมื่อมีเพื่อนร่วมงานขอเปลี่ยนเวรกับคุณ\nคำขอจะปรากฏที่นี่',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 14,
                color: Color(0xFF64748B),
                height: 1.5,
              ),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      physics: const BouncingScrollPhysics(),
      padding: const EdgeInsets.fromLTRB(24, 16, 24, 100),
      itemCount: provider.approvals.length,
      itemBuilder: (context, index) {
        final request = provider.approvals[index];
        return _buildAnimatedListItem(index, _buildApprovalCard(request));
      },
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

  Widget _buildApprovalCard(GuardChangeRequest request) {
    return Container(
      margin: const EdgeInsets.only(bottom: 24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 30,
            offset: const Offset(0, 15),
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
                      padding: const EdgeInsets.all(3),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        gradient: const LinearGradient(
                          colors: [Color(0xFF6366F1), Color(0xFF8B5CF6)],
                        ),
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFF6366F1).withOpacity(0.3),
                            blurRadius: 10,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: CircleAvatar(
                        radius: 26,
                        backgroundColor: Colors.white,
                        child: Text(
                          (request.user?.name != null &&
                                  request.user!.name.isNotEmpty)
                              ? request.user!.name[0].toUpperCase()
                              : '?',
                          style: const TextStyle(
                            color: Color(0xFF6366F1),
                            fontWeight: FontWeight.w900,
                            fontSize: 20,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            request.user?.fullName ?? 'ไม่ระบุชื่อ',
                            style: const TextStyle(
                              fontWeight: FontWeight.w900,
                              fontSize: 18,
                              color: Color(0xFF1E293B),
                              letterSpacing: -0.5,
                            ),
                          ),
                          Text(
                            'ต้องการให้ท่านปฏิบัติเวรแทน',
                            style: TextStyle(
                              color: const Color(0xFF64748B).withOpacity(0.8),
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF8FAFC),
                    borderRadius: BorderRadius.circular(24),
                    border: Border.all(color: const Color(0xFFF1F5F9)),
                  ),
                  child: Column(
                    children: [
                      _buildDetailRow(
                        'ตำแหน่งเวร',
                        request.dutyPositionThai,
                        Icons.security_rounded,
                      ),
                      Padding(
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        child: Divider(
                          height: 1,
                          color: Colors.grey.withOpacity(0.1),
                        ),
                      ),
                      _buildDetailRow(
                        'วันที่ปฏิบัติ',
                        request.formattedDutyDate,
                        Icons.calendar_today_rounded,
                      ),
                    ],
                  ),
                ),
                if (request.remarks != null && request.remarks!.isNotEmpty) ...[
                  const SizedBox(height: 16),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 12,
                    ),
                    decoration: BoxDecoration(
                      color: const Color(0xFFFFF7ED),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: const Color(0xFFFFEDD5)),
                    ),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Icon(
                          Icons.format_quote_rounded,
                          color: Color(0xFFF97316),
                          size: 20,
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            '${request.remarks}',
                            style: const TextStyle(
                              color: Color(0xFF9A3412),
                              fontSize: 13,
                              fontWeight: FontWeight.w500,
                              fontStyle: FontStyle.italic,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
                const SizedBox(height: 28),
                Row(
                  children: [
                    Expanded(
                      child: TextButton(
                        onPressed: () {
                          HapticFeedback.lightImpact();
                          _showRejectDialog(request.id);
                        },
                        style: TextButton.styleFrom(
                          foregroundColor: const Color(0xFFEF4444),
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                            side: BorderSide(
                              color: const Color(0xFFEF4444).withOpacity(0.1),
                              width: 1.5,
                            ),
                          ),
                        ),
                        child: const Text(
                          'ปฏิเสธ',
                          style: TextStyle(
                            fontWeight: FontWeight.w800,
                            fontSize: 15,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(16),
                          gradient: const LinearGradient(
                            colors: [Color(0xFF10B981), Color(0xFF059669)],
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: const Color(0xFF10B981).withOpacity(0.3),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: ElevatedButton(
                          onPressed: () {
                            HapticFeedback.lightImpact();
                            _showApproveDialog(request.id);
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.transparent,
                            foregroundColor: Colors.white,
                            shadowColor: Colors.transparent,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                          ),
                          child: const Text(
                            'ยืนยันรับเวร',
                            style: TextStyle(
                              fontWeight: FontWeight.w800,
                              fontSize: 15,
                            ),
                          ),
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

  Widget _buildDetailRow(String label, String value, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 18, color: const Color(0xFF94A3B8)),
        const SizedBox(width: 12),
        Text(
          label,
          style: const TextStyle(
            color: Color(0xFF64748B),
            fontSize: 13,
            fontWeight: FontWeight.w600,
          ),
        ),
        const Spacer(),
        Text(
          value,
          style: const TextStyle(
            color: Color(0xFF1E293B),
            fontSize: 14,
            fontWeight: FontWeight.w800,
          ),
        ),
      ],
    );
  }

  void _showApproveDialog(int id) {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    showDialog(
      context: context,
      builder: (context) => SignatureDialog(
        isApprove: true,
        savedSignatureUrl: authProvider.user?.signatureUrl,
      ),
    ).then((result) async {
      if (result != null) {
        final provider = Provider.of<GuardChangeProvider>(
          context,
          listen: false,
        );
        final success = await provider.approveRequest(
          id,
          comment: result['comment'],
          signature: result['signature'],
          useSavedSignature: result['useSavedSignature'] ?? false,
        );
        if (success && mounted) {
          _showSnackBar(
            'ยืนยันการรับเปลี่ยนเวรเรียบร้อยแล้ว',
            AppTheme.success,
          );
        }
      }
    });
  }

  void _showRejectDialog(int id) {
    final controller = TextEditingController();
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text(
          'ระบุเหตุผลที่ปฏิเสธ',
          style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18),
        ),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        content: TextField(
          controller: controller,
          decoration: InputDecoration(
            hintText: 'เหตุผล...',
            filled: true,
            fillColor: const Color(0xFFF8FAFC),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(16),
              borderSide: BorderSide.none,
            ),
          ),
          maxLines: 3,
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text(
              'ยกเลิก',
              style: TextStyle(
                fontWeight: FontWeight.w700,
                color: Color(0xFF64748B),
              ),
            ),
          ),
          ElevatedButton(
            onPressed: () async {
              if (controller.text.isEmpty) return;
              final provider = Provider.of<GuardChangeProvider>(
                context,
                listen: false,
              );
              final success = await provider.rejectRequest(
                id,
                comment: controller.text,
              );
              if (success && mounted) {
                Navigator.pop(context);
                _showSnackBar('ปฏิเสธคำขอเรียบร้อยแล้ว', AppTheme.error);
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.error,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
            ),
            child: const Text(
              'ยืนยันการปฏิเสธ',
              style: TextStyle(fontWeight: FontWeight.w700),
            ),
          ),
        ],
      ),
    );
  }

  void _showSnackBar(String message, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            Icon(
              color == AppTheme.success
                  ? Icons.check_circle_rounded
                  : Icons.error_rounded,
              color: Colors.white,
            ),
            const SizedBox(width: 12),
            Text(
              message,
              style: const TextStyle(
                fontWeight: FontWeight.w700,
                fontFamily: 'NotoSansThai',
              ),
            ),
          ],
        ),
        backgroundColor: color,
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.all(24),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        elevation: 4,
      ),
    );
  }
}
