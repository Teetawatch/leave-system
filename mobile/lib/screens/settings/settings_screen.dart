import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/theme_provider.dart';
import '../../providers/security_provider.dart';
import '../../config/app_theme.dart';
import '../security/pin_screen.dart';
import '../../widgets/animated_background.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: Text(
          'การตั้งค่า',
          style: GoogleFonts.kanit(
            fontSize: 22,
            fontWeight: FontWeight.w700,
            color: AppTheme.textMain,
          ),
        ),
        centerTitle: false,
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: Container(
          margin: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.5),
            shape: BoxShape.circle,
          ),
          child: IconButton(
            icon: const Icon(
              Icons.arrow_back_ios_new_rounded,
              size: 20,
              color: AppTheme.textMain,
            ),
            onPressed: () => Navigator.pop(context),
          ),
        ),
      ),
      body: Stack(
        children: [
          const Positioned.fill(child: AnimatedBackground()),
          SafeArea(
            child: ListView(
              padding: const EdgeInsets.all(24),
              children: [
                _buildAppearanceSection(context),
                const SizedBox(height: 24),
                _buildSecuritySection(context),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAppearanceSection(BuildContext context) {
    final themeProvider = Provider.of<ThemeProvider>(context);
    return Container(
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
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Text(
              'การแสดงผล',
              style: GoogleFonts.kanit(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: AppTheme.textMain,
              ),
            ),
          ),
          SwitchListTile(
            title: Text(
              'Dark Mode',
              style: GoogleFonts.kanit(fontSize: 16, color: AppTheme.textMain),
            ),
            subtitle: Text(
              'ใช้งานโหมดมืดปรับแสงให้นุ่มนวล',
              style: GoogleFonts.sarabun(fontSize: 14, color: AppTheme.textSub),
            ),
            value: themeProvider.isDarkMode,
            onChanged: (value) {
              themeProvider.toggleTheme(value);
            },
            activeThumbColor: AppTheme.primary,
          ),
        ],
      ),
    );
  }

  Widget _buildSecuritySection(BuildContext context) {
    return Consumer<SecurityProvider>(
      builder: (context, security, _) {
        return Container(
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
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.all(16),
                child: Text(
                  'ความปลอดภัย',
                  style: GoogleFonts.kanit(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: AppTheme.textMain,
                  ),
                ),
              ),
              SwitchListTile(
                title: Text(
                  'ล็อกแอปพลิเคชัน',
                  style: GoogleFonts.kanit(
                    fontSize: 16,
                    color: AppTheme.textMain,
                  ),
                ),
                subtitle: Text(
                  'ใช้รหัส PIN เพื่อเข้าใช้งาน',
                  style: GoogleFonts.sarabun(
                    fontSize: 14,
                    color: AppTheme.textSub,
                  ),
                ),
                value: security.isPinEnabled,
                onChanged: (value) {
                  if (value) {
                    // Navigate to setup PIN
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => PinScreen(
                          mode: PinMode.setup,
                          onSuccess: () {
                            Navigator.pop(context);
                          },
                          onCancel: () {
                            Navigator.pop(context);
                          },
                        ),
                      ),
                    );
                  } else {
                    // Navigate to disable PIN (verify first)
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => PinScreen(
                          mode: PinMode.disable,
                          onSuccess: () {
                            Navigator.pop(context);
                          },
                          onCancel: () {
                            Navigator.pop(context);
                          },
                        ),
                      ),
                    );
                  }
                },
                activeThumbColor: AppTheme.primary,
              ),
              if (security.isPinEnabled) ...[
                Divider(
                  height: 1,
                  color: Colors.grey[200],
                  indent: 16,
                  endIndent: 16,
                ),
                ListTile(
                  title: Text(
                    'เปลี่ยนรหัส PIN',
                    style: GoogleFonts.kanit(
                      fontSize: 16,
                      color: AppTheme.textMain,
                    ),
                  ),
                  trailing: Icon(
                    Icons.arrow_forward_ios_rounded,
                    size: 16,
                    color: Colors.grey[300],
                  ),
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => PinScreen(
                          mode: PinMode
                              .setup, // Setup acts as change if we just overwrite
                          onSuccess: () {
                            Navigator.pop(context);
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                content: Text(
                                  'เปลี่ยนรหัส PIN สำเร็จ',
                                  style: GoogleFonts.kanit(),
                                ),
                                backgroundColor: AppTheme.success,
                                behavior: SnackBarBehavior.floating,
                              ),
                            );
                          },
                          onCancel: () {
                            Navigator.pop(context);
                          },
                        ),
                      ),
                    );
                  },
                ),
                Divider(
                  height: 1,
                  color: Colors.grey[200],
                  indent: 16,
                  endIndent: 16,
                ),
                SwitchListTile(
                  title: Text(
                    'สแกนนิ้ว / Face ID',
                    style: GoogleFonts.kanit(
                      fontSize: 16,
                      color: AppTheme.textMain,
                    ),
                  ),
                  subtitle: Text(
                    'เข้าใช้งานด้วย Biometrics',
                    style: GoogleFonts.sarabun(
                      fontSize: 14,
                      color: AppTheme.textSub,
                    ),
                  ),
                  value: security.isBiometricEnabled,
                  onChanged: (value) async {
                    try {
                      await security.toggleBiometric(value);
                    } catch (e) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text(
                            'เกิดข้อผิดพลาด: ${e.toString()}',
                            style: GoogleFonts.kanit(),
                          ),
                          backgroundColor: AppTheme.error,
                        ),
                      );
                    }
                  },
                  activeThumbColor: AppTheme.primary,
                ),
              ],
            ],
          ),
        );
      },
    );
  }
}
