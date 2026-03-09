import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../config/api_config.dart';
import '../config/app_theme.dart';
import '../widgets/user_avatar.dart';
import '../models/leave_balance.dart';
import '../providers/auth_provider.dart';
import '../services/api_service.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final ApiService _api = ApiService();
  List<LeaveBalance> _balances = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    try {
      final response = await _api.get(ApiConfig.leaveBalance);
      final balanceData = response['data'];
      if (balanceData != null && balanceData['balances'] != null) {
        _balances = (balanceData['balances'] as List)
            .map((b) => LeaveBalance.fromJson(b))
            .toList();
      }
    } catch (e) {
      debugPrint('Profile load error: $e');
    }
    if (mounted) setState(() => _isLoading = false);
  }

  Future<void> _logout() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('ออกจากระบบ'),
        content: const Text('คุณต้องการออกจากระบบหรือไม่?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('ยกเลิก')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.error),
            child: const Text('ออกจากระบบ'),
          ),
        ],
      ),
    );

    if (confirmed == true && mounted) {
      await context.read<AuthProvider>().logout();
      if (mounted) {
        Navigator.of(context).pushNamedAndRemoveUntil('/login', (route) => false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return Scaffold(
      appBar: AppBar(
        title: Text('โปรไฟล์', style: AppTheme.heading(18)),
        actions: [
          IconButton(
            onPressed: _logout,
            icon: const Icon(Icons.logout_rounded, color: AppTheme.error),
            tooltip: 'ออกจากระบบ',
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await context.read<AuthProvider>().refreshUser();
          await _loadData();
        },
        color: AppTheme.primary,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(20),
          child: Column(
            children: [
              // Profile Header
              _buildProfileHeader(user),
              const SizedBox(height: 24),

              // Info Section
              _buildInfoSection(user),
              const SizedBox(height: 24),

              // Leave Balance
              _buildLeaveBalanceSection(),
              const SizedBox(height: 24),

              // Logout Button
              SizedBox(
                width: double.infinity,
                height: 54,
                child: OutlinedButton.icon(
                  onPressed: _logout,
                  icon: const Icon(Icons.logout_rounded),
                  label: Text('ออกจากระบบ', style: GoogleFonts.prompt(fontWeight: FontWeight.w600)),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: AppTheme.error,
                    side: const BorderSide(color: AppTheme.error),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    padding: const EdgeInsets.symmetric(vertical: 14),
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // App version
              Text(
                'NASS Leave System v1.0.0',
                style: AppTheme.body(12, color: AppTheme.textMuted),
              ),
              const SizedBox(height: 30),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildProfileHeader(dynamic user) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: AppTheme.primaryGradient,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: AppTheme.primary.withValues(alpha: 0.3),
            blurRadius: 16,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        children: [
          UserAvatar(
            name: user?.name ?? 'U',
            imageUrl: user?.avatarUrl,
            radius: 44,
            backgroundColor: Colors.white.withValues(alpha: 0.2),
            textColor: Colors.white,
          ),
          const SizedBox(height: 14),
          Text(
            user?.displayName ?? '',
            style: GoogleFonts.prompt(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w600),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 6),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.15),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: Colors.white.withValues(alpha: 0.2)),
            ),
            child: Text(
              user?.roleLabel ?? '',
              style: GoogleFonts.prompt(
                color: Colors.white.withValues(alpha: 0.9),
                fontSize: 13,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoSection(dynamic user) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(18),
        boxShadow: AppTheme.softShadow,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: AppTheme.primary.withValues(alpha: 0.08),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Icon(Icons.info_outline_rounded, size: 18, color: AppTheme.primary),
              ),
              const SizedBox(width: 10),
              Text('ข้อมูลส่วนตัว', style: AppTheme.heading(16)),
            ],
          ),
          const SizedBox(height: 16),
          _infoItem(Icons.email_outlined, 'อีเมล', user?.email ?? '-'),
          const Divider(height: 20),
          _infoItem(Icons.business_rounded, 'หน่วยงาน/แผนก', user?.department ?? '-'),
          const Divider(height: 20),
          _infoItem(Icons.work_outline_rounded, 'ตำแหน่ง', user?.position ?? '-'),
          const Divider(height: 20),
          _infoItem(Icons.military_tech_rounded, 'ยศ', user?.rank ?? '-'),
          if (user?.supervisor != null) ...[
            const Divider(height: 20),
            _infoItem(Icons.person_outline_rounded, 'หัวหน้างาน', user.supervisor!['name'] ?? '-'),
          ],
        ],
      ),
    );
  }

  Widget _infoItem(IconData icon, String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 20, color: AppTheme.textMuted),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: AppTheme.body(12, color: AppTheme.textMuted)),
              const SizedBox(height: 2),
              Text(value, style: AppTheme.heading(15, weight: FontWeight.w500)),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildLeaveBalanceSection() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(18),
        boxShadow: AppTheme.softShadow,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: AppTheme.primary.withValues(alpha: 0.08),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Icon(Icons.calendar_today_rounded, size: 18, color: AppTheme.primary),
              ),
              const SizedBox(width: 10),
              Text('สรุปวันลาประจำปี', style: AppTheme.heading(16)),
            ],
          ),
          const SizedBox(height: 16),
          if (_isLoading)
            const Center(child: CircularProgressIndicator(color: AppTheme.primary))
          else if (_balances.isEmpty)
            Center(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Text('ยังไม่มีข้อมูลวันลา', style: AppTheme.body(14, color: AppTheme.textMuted)),
              ),
            )
          else
            ..._balances.asMap().entries.map((entry) {
              final i = entry.key;
              final b = entry.value;
              final colors = _getColor(i);
              final percentage = b.totalDays > 0 ? b.usedDays / b.totalDays : 0.0;

              return Container(
                margin: EdgeInsets.only(bottom: i < _balances.length - 1 ? 14 : 0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          b.leaveType?.name ?? 'ลา',
                          style: AppTheme.heading(14),
                        ),
                        Text(
                          '${b.usedDays.toStringAsFixed(0)}/${b.totalDays.toStringAsFixed(0)} วัน',
                          style: AppTheme.body(13, color: AppTheme.textSecondary),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    ClipRRect(
                      borderRadius: BorderRadius.circular(6),
                      child: LinearProgressIndicator(
                        value: percentage.clamp(0.0, 1.0),
                        backgroundColor: colors.withValues(alpha: 0.15),
                        valueColor: AlwaysStoppedAnimation(colors),
                        minHeight: 8,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'คงเหลือ ${b.remainingDays.toStringAsFixed(0)} วัน',
                      style: AppTheme.body(12, color: colors, weight: FontWeight.w600),
                    ),
                  ],
                ),
              );
            }),
        ],
      ),
    );
  }

  Color _getColor(int index) {
    final colors = [AppTheme.primary, AppTheme.success, AppTheme.warning, AppTheme.secondary, AppTheme.error];
    return colors[index % colors.length];
  }
}
