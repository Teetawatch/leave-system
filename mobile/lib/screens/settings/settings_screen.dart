import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/theme_provider.dart';
import '../../providers/security_provider.dart';
import '../../config/app_theme.dart';
import '../security/pin_screen.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('การตั้งค่า')),
      body: ListView(
        children: [
          _buildAppearanceSection(context),
          const SizedBox(height: 20),
          _buildSecuritySection(context),
        ],
      ),
    );
  }

  Widget _buildAppearanceSection(BuildContext context) {
    final themeProvider = Provider.of<ThemeProvider>(context);
    return Card(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Text(
              'การแสดงผล',
              style: Theme.of(context).textTheme.titleMedium,
            ),
          ),
          SwitchListTile(
            title: const Text('Dark Mode'),
            subtitle: const Text('ใช้งานโหมดมืดปรับแสงให้นุ่มนวล'),
            value: themeProvider.isDarkMode,
            onChanged: (value) {
              themeProvider.toggleTheme(value);
            },
            activeColor: AppTheme.primary,
          ),
        ],
      ),
    );
  }

  Widget _buildSecuritySection(BuildContext context) {
    return Consumer<SecurityProvider>(
      builder: (context, security, _) {
        return Card(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.all(16),
                child: Text(
                  'ความปลอดภัย',
                  style: Theme.of(context).textTheme.titleMedium,
                ),
              ),
              SwitchListTile(
                title: const Text('ล็อกแอปพลิเคชัน'),
                subtitle: const Text('ใช้รหัส PIN เพื่อเข้าใช้งาน'),
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
                activeColor: AppTheme.primary,
              ),
              if (security.isPinEnabled) ...[
                Divider(color: Theme.of(context).dividerColor),
                ListTile(
                  title: const Text('เปลี่ยนรหัส PIN'),
                  trailing: const Icon(Icons.arrow_forward_ios, size: 16),
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
                              const SnackBar(
                                content: Text('เปลี่ยนรหัส PIN สำเร็จ'),
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
                Divider(color: Theme.of(context).dividerColor),
                SwitchListTile(
                  title: const Text('สแกนนิ้ว / Face ID'),
                  subtitle: const Text('เข้าใช้งานด้วย Biometrics'),
                  value: security.isBiometricEnabled,
                  onChanged: (value) async {
                    try {
                      await security.toggleBiometric(value);
                    } catch (e) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text('เกิดข้อผิดพลาด: ${e.toString()}'),
                        ),
                      );
                    }
                  },
                  activeColor: AppTheme.primary,
                ),
              ],
            ],
          ),
        );
      },
    );
  }
}
