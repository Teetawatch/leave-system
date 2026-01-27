import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:image_picker/image_picker.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/auth_provider.dart';
import '../config/app_theme.dart';
import 'settings/settings_screen.dart';
import '../widgets/animated_background.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final ImagePicker _picker = ImagePicker();

  Future<void> _pickAvatar() async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    try {
      final XFile? image = await _picker.pickImage(
        source: ImageSource.gallery,
        imageQuality: 80,
        maxWidth: 1000,
      );

      if (image != null) {
        final success = await authProvider.updateProfile(
          avatarPath: image.path,
        );

        if (success && mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                'อัปโหลดรูปโปรไฟล์สำเร็จ',
                style: GoogleFonts.kanit(),
              ),
              backgroundColor: AppTheme.success,
              behavior: SnackBarBehavior.floating,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
            ),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('เกิดข้อผิดพลาด: $e', style: GoogleFonts.kanit()),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final user = authProvider.user;

    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: Text(
          'โปรไฟล์',
          style: GoogleFonts.kanit(
            color: AppTheme.textMain,
            fontWeight: FontWeight.bold,
            fontSize: 22,
          ),
        ),
        centerTitle: false,
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: Navigator.canPop(context)
            ? Container(
                margin: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.5),
                  shape: BoxShape.circle,
                ),
                child: IconButton(
                  icon: const Icon(Icons.arrow_back_ios_new_rounded),
                  color: AppTheme.textMain,
                  iconSize: 20,
                  onPressed: () => Navigator.pop(context),
                ),
              )
            : null,
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 16),
            child: TextButton(
              onPressed: () {
                // Edit profile action - for now just show snackbar or could trigger avatar picker
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(
                      'ฟีเจอร์นี้ยังไม่เปิดใช้งาน',
                      style: GoogleFonts.kanit(),
                    ),
                  ),
                );
              },
              child: Text(
                'แก้ไข',
                style: GoogleFonts.kanit(
                  color: AppTheme.primary,
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                ),
              ),
            ),
          ),
        ],
      ),
      body: Stack(
        children: [
          const Positioned.fill(child: AnimatedBackground()),
          SafeArea(
            child: SingleChildScrollView(
              child: Padding(
                padding: const EdgeInsets.all(24.0),
                child: Column(
                  children: [
                    // 1. Profile Avatar Section
                    Center(
                      child: Stack(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(4),
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              border: Border.all(color: Colors.white, width: 2),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(0.1),
                                  blurRadius: 10,
                                  offset: const Offset(0, 4),
                                ),
                              ],
                            ),
                            child: GestureDetector(
                              onTap: authProvider.isLoading
                                  ? null
                                  : _pickAvatar,
                              child: CircleAvatar(
                                radius: 50,
                                backgroundColor: Colors.grey[200],
                                backgroundImage: user?.avatarUrl != null
                                    ? NetworkImage(user!.avatarUrl!)
                                    : null,
                                child: user?.avatarUrl == null
                                    ? const Icon(
                                        Icons.person,
                                        size: 50,
                                        color: Colors.grey,
                                      )
                                    : null,
                              ),
                            ),
                          ),
                          // Online Status Dot
                          Positioned(
                            bottom: 8,
                            right: 8,
                            child: Container(
                              width: 16,
                              height: 16,
                              decoration: BoxDecoration(
                                color: const Color(0xFF22C55E), // Green
                                shape: BoxShape.circle,
                                border: Border.all(
                                  color: Colors.white,
                                  width: 2,
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Name and Position
                    Text(
                      user?.fullName ?? 'ไม่ระบุชื่อ',
                      style: GoogleFonts.kanit(
                        fontSize: 22,
                        fontWeight: FontWeight.bold,
                        color: AppTheme.textMain,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      user?.position ?? 'ไม่ระบุตำแหน่ง',
                      style: GoogleFonts.sarabun(
                        fontSize: 14,
                        color: AppTheme.textSub,
                        fontWeight: FontWeight.w500,
                      ),
                    ),

                    const SizedBox(height: 32),

                    // 2. Personal Information Section
                    _buildSectionHeader('ข้อมูลส่วนตัว'),
                    Container(
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.02),
                            blurRadius: 10,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Column(
                        children: [
                          _buildListTile(
                            icon: Icons.email_outlined,
                            title: 'อีเมล',
                            subtitle: user?.email ?? '-',
                          ),
                          _buildDivider(),
                          _buildListTile(
                            icon: Icons.phone_outlined,
                            title: 'เบอร์โทรศัพท์',
                            subtitle: user?.phoneNumber ?? '-',
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 24),

                    // 3. Work Details Section
                    _buildSectionHeader('ข้อมูลงาน'),
                    Container(
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.02),
                            blurRadius: 10,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Column(
                        children: [
                          _buildListTile(
                            icon: Icons.badge_outlined,
                            title: 'รหัสพนักงาน',
                            subtitle: '#${user?.id ?? '-'}',
                          ),
                          _buildDivider(),
                          _buildListTile(
                            icon: Icons.business_outlined,
                            title: 'แผนก',
                            subtitle: user?.department ?? '-',
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 24),

                    // 4. Account Settings Section
                    _buildSectionHeader('ตั้งค่าบัญชี'),
                    Container(
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.02),
                            blurRadius: 10,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Column(
                        children: [
                          _buildListTile(
                            icon: Icons.lock_outline_rounded,
                            title: 'เปลี่ยนรหัสผ่าน',
                            showArrow: true,
                            onTap: () {
                              // TODO: Navigate to change password screen
                            },
                          ),
                          _buildDivider(),
                          _buildListTile(
                            icon: Icons.notifications_none_rounded,
                            title: 'การแจ้งเตือน',
                            showArrow: true,
                            onTap: () {
                              // TODO: Navigate to notifications settings
                            },
                          ),
                          _buildDivider(),
                          _buildListTile(
                            icon: Icons.settings_outlined,
                            title: 'ความปลอดภัยและอื่นๆ',
                            showArrow: true,
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (context) => const SettingsScreen(),
                                ),
                              );
                            },
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 32),

                    // 5. Log Out Button
                    SizedBox(
                      width: double.infinity,
                      child: OutlinedButton.icon(
                        onPressed: () {
                          authProvider.logout();
                        },
                        icon: const Icon(
                          Icons.logout,
                          color: Color(0xFFEF4444),
                        ),
                        label: Text(
                          'ออกจากระบบ',
                          style: GoogleFonts.kanit(
                            color: const Color(0xFFEF4444),
                            fontWeight: FontWeight.bold,
                            fontSize: 16,
                          ),
                        ),
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          side: BorderSide(
                            color: const Color(0xFFEF4444).withOpacity(0.2),
                          ),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          backgroundColor: Colors.white,
                        ),
                      ),
                    ),
                    const SizedBox(height: 30),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12, left: 4),
      child: Align(
        alignment: Alignment.centerLeft,
        child: Text(
          title,
          style: GoogleFonts.kanit(
            fontSize: 14,
            fontWeight: FontWeight.bold,
            color: AppTheme.textSub,
            letterSpacing: 1.0,
          ),
        ),
      ),
    );
  }

  Widget _buildListTile({
    required IconData icon,
    required String title,
    String? subtitle,
    bool showArrow = false,
    VoidCallback? onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: AppTheme.primary.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: Icon(icon, color: AppTheme.primary, size: 20),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      showArrow ? title : title.toUpperCase(),
                      style: showArrow
                          ? GoogleFonts.kanit(
                              fontSize: 16,
                              fontWeight: FontWeight.w600,
                              color: AppTheme.textMain,
                            )
                          : GoogleFonts.kanit(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                              color: AppTheme.textSub,
                            ),
                    ),
                    if (!showArrow && subtitle != null) ...[
                      const SizedBox(height: 4),
                      Text(
                        subtitle,
                        style: GoogleFonts.sarabun(
                          fontSize: 16,
                          fontWeight: FontWeight.w500,
                          color: AppTheme.textMain,
                        ),
                      ),
                    ],
                  ],
                ),
              ),
              if (showArrow || (!showArrow && subtitle != null))
                Icon(
                  Icons.arrow_forward_ios_rounded,
                  size: 16,
                  color: Colors.grey[300],
                ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDivider() {
    return Divider(
      height: 1,
      thickness: 1,
      color: Colors.grey[100],
      indent: 16,
      endIndent: 16,
    );
  }
}
