import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'dart:ui';
import '../config/app_theme.dart';
import '../models/guard_change_model.dart';
import '../providers/guard_change_provider.dart';
import '../providers/auth_provider.dart';
import '../widgets/signature_dialog.dart';

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
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: Colors.white,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text(
          'คำขอถึงฉัน',
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
                colors: [Color(0xFFF8FAFC), Colors.white, Color(0xFFF1F5F9)],
              ),
            ),
          ),
          _buildFloatingCircle(
            top: -40,
            right: -40,
            size: 220,
            color: Colors.indigo.withOpacity(0.06),
          ),
          _buildFloatingCircle(
            bottom: -50,
            left: -50,
            size: 280,
            color: Colors.blue.withOpacity(0.05),
          ),

          SafeArea(
            child: RefreshIndicator(
              onRefresh: () => provider.fetchApprovals(),
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
    if (provider.isLoading && provider.approvals.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    if (provider.approvals.isEmpty) {
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
                Icons.assignment_turned_in_rounded,
                size: 64,
                color: AppTheme.textSub.withOpacity(0.2),
              ),
            ),
            const SizedBox(height: 24),
            const Text(
              'ไม่มีคำขอรอยืนยัน',
              style: TextStyle(
                color: AppTheme.textSub,
                fontSize: 18,
                fontWeight: FontWeight.w700,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'เมื่อมีเพื่อนร่วมงานขอเปลี่ยนเวรกับคุณ\nคำขอจะปรากฏที่นี่',
              textAlign: TextAlign.center,
              style: TextStyle(
                color: AppTheme.textSub.withOpacity(0.5),
                fontSize: 14,
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
        return _buildApprovalCard(request);
      },
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
            color: Colors.black.withOpacity(0.04),
            blurRadius: 25,
            offset: const Offset(0, 12),
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
                    CircleAvatar(
                      radius: 24,
                      backgroundColor: AppTheme.primary.withOpacity(0.1),
                      child: Text(
                        request.user?.name.substring(0, 1) ?? '?',
                        style: const TextStyle(
                          color: AppTheme.primary,
                          fontWeight: FontWeight.w900,
                          fontSize: 18,
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            request.user?.name ?? 'ไม่ระบุชื่อ',
                            style: const TextStyle(
                              fontWeight: FontWeight.w900,
                              fontSize: 17,
                              color: AppTheme.textMain,
                            ),
                          ),
                          const Text(
                            'ต้องการให้ท่านปฏิบัติเวรแทน',
                            style: TextStyle(
                              color: AppTheme.textSub,
                              fontSize: 12,
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
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Color(0xFFF8FAFC),
                    borderRadius: BorderRadius.circular(24),
                    border: Border.all(color: AppTheme.border.withOpacity(0.3)),
                  ),
                  child: Column(
                    children: [
                      _buildDetailRow(
                        'ตำแหน่งเวร',
                        request.dutyPositionThai,
                        Icons.security_rounded,
                      ),
                      const Padding(
                        padding: EdgeInsets.symmetric(vertical: 8),
                        child: Divider(height: 1),
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
                  Text(
                    'หมายเหตุ: ${request.remarks}',
                    style: TextStyle(
                      color: AppTheme.textSub.withOpacity(0.8),
                      fontSize: 13,
                      fontStyle: FontStyle.italic,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],
                const SizedBox(height: 28),
                Row(
                  children: [
                    Expanded(
                      child: TextButton(
                        onPressed: () => _showRejectDialog(request.id),
                        style: TextButton.styleFrom(
                          foregroundColor: AppTheme.error,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                        child: const Text(
                          'ปฏิเสธ',
                          style: TextStyle(
                            fontWeight: FontWeight.w900,
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
                            colors: [Colors.green, Colors.teal],
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.green.withOpacity(0.2),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: ElevatedButton(
                          onPressed: () => _showApproveDialog(request.id),
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
                              fontWeight: FontWeight.w900,
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
        Icon(icon, size: 16, color: AppTheme.textSub),
        const SizedBox(width: 12),
        Text(
          label,
          style: const TextStyle(
            color: AppTheme.textSub,
            fontSize: 13,
            fontWeight: FontWeight.w600,
          ),
        ),
        const Spacer(),
        Text(
          value,
          style: const TextStyle(
            color: AppTheme.textMain,
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
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: const Text(
                'ยืนยันการรับเปลี่ยนเวรเรียบร้อยแล้ว',
                style: TextStyle(fontWeight: FontWeight.w600),
              ),
              backgroundColor: AppTheme.success,
              behavior: SnackBarBehavior.floating,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
            ),
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
          style: TextStyle(fontWeight: FontWeight.w900),
        ),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        content: TextField(
          controller: controller,
          decoration: const InputDecoration(hintText: 'ระบุเหตุผล...'),
          maxLines: 3,
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('ยกเลิก'),
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
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: const Text('ปฏิเสธคำขอเรียบร้อยแล้ว'),
                    backgroundColor: AppTheme.error,
                    behavior: SnackBarBehavior.floating,
                  ),
                );
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.error,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
            child: const Text('ยืนยันการปฏิเสธ'),
          ),
        ],
      ),
    );
  }
}
