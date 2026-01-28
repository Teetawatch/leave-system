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
  Future<void> _pickAvatar() async {
    final ImagePicker picker = ImagePicker();
    try {
      final XFile? image = await picker.pickImage(
        source: ImageSource.gallery,
        imageQuality: 50,
        maxWidth: 500,
      );

      if (image != null) {
        final authProvider = Provider.of<AuthProvider>(context, listen: false);
        final success = await authProvider.updateProfile(
          avatarPath: image.path,
        );

        if (mounted) {
          if (success) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(
                  'อัปเดตรูปโปรไฟล์สำเร็จ',
                  style: GoogleFonts.kanit(),
                ),
                backgroundColor: Colors.green,
              ),
            );
          } else {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(
                  'เกิดข้อผิดพลาดในการอัปเดตรูปโปรไฟล์',
                  style: GoogleFonts.kanit(),
                ),
                backgroundColor: Colors.red,
              ),
            );
          }
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

  Future<void> _pickSignature() async {
    final ImagePicker picker = ImagePicker();
    try {
      final XFile? image = await picker.pickImage(
        source: ImageSource.gallery,
        imageQuality: 80,
        maxWidth: 800,
      );

      if (image != null) {
        final authProvider = Provider.of<AuthProvider>(context, listen: false);
        final success = await authProvider.updateProfile(
          signaturePath: image.path,
        );

        if (mounted) {
          if (success) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(
                  'อัปเดตลายเซ็นสำเร็จ',
                  style: GoogleFonts.kanit(),
                ),
                backgroundColor: Colors.green,
              ),
            );
          } else {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(
                  'เกิดข้อผิดพลาดในการอัปเดตลายเซ็น',
                  style: GoogleFonts.kanit(),
                ),
                backgroundColor: Colors.red,
              ),
            );
          }
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
                            title: 'รหัส ID',
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

                    // 4. Digital Signature Section
                    _buildSectionHeader('ลายเซ็นดิจิทัล'),
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(20),
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
                          if (user?.signatureUrl != null)
                            Container(
                              margin: const EdgeInsets.only(bottom: 20),
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: const Color(0xFFF8FAFC),
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(
                                  color: const Color(0xFFF1F5F9),
                                ),
                              ),
                              child: Image.network(
                                user!.signatureUrl!,
                                height: 100,
                                fit: BoxFit.contain,
                                errorBuilder: (context, error, stackTrace) =>
                                    const Icon(
                                      Icons.error_outline_rounded,
                                      color: Colors.red,
                                    ),
                              ),
                            )
                          else
                            Container(
                              margin: const EdgeInsets.only(bottom: 20),
                              padding: const EdgeInsets.all(32),
                              decoration: BoxDecoration(
                                color: const Color(0xFFF8FAFC),
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(
                                  color: const Color(0xFFF1F5F9),
                                  style: BorderStyle.solid,
                                ),
                              ),
                              child: Icon(
                                Icons.draw_rounded,
                                size: 48,
                                color: Colors.blue.withOpacity(0.3),
                              ),
                            ),
                          SizedBox(
                            width: double.infinity,
                            child: ElevatedButton.icon(
                              onPressed: authProvider.isLoading
                                  ? null
                                  : _pickSignature,
                              icon: const Icon(Icons.upload_rounded, size: 20),
                              label: Text(
                                'อัปโหลดลายเซ็นใหม่',
                                style: GoogleFonts.kanit(
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: AppTheme.primary,
                                foregroundColor: Colors.white,
                                padding: const EdgeInsets.symmetric(
                                  vertical: 14,
                                ),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(12),
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 24),

                    // 5. Account Settings Section
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
