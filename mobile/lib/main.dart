import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'config/app_config.dart';
import 'providers/auth_provider.dart';
import 'providers/leave_provider.dart';
import 'providers/guard_change_provider.dart';
import 'providers/news_provider.dart';
import 'providers/notification_provider.dart';
import 'providers/directory_provider.dart';
import 'providers/theme_provider.dart';
import 'providers/security_provider.dart';
import 'screens/login_screen.dart';
import 'screens/main_navigation_screen.dart';
import 'screens/security/pin_screen.dart';
import 'config/app_theme.dart';

import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'services/notification_service.dart';
import 'package:intl/date_symbol_data_local.dart';

@pragma('vm:entry-point')
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  debugPrint("Handling a background message: ${message.messageId}");
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('th_TH', null);

  try {
    await Firebase.initializeApp();
    FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);
    await NotificationService().initialize();
  } catch (e) {
    debugPrint('Firebase initialization failed: $e');
  }

  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => LeaveProvider()),
        ChangeNotifierProvider(create: (_) => GuardChangeProvider()),
        ChangeNotifierProvider(create: (_) => NewsProvider()),
        ChangeNotifierProvider(create: (_) => NotificationProvider()),
        ChangeNotifierProvider(create: (_) => DirectoryProvider()),
        ChangeNotifierProvider(create: (_) => ThemeProvider()),
        ChangeNotifierProvider(create: (_) => SecurityProvider()),
      ],
      child: Consumer<ThemeProvider>(
        builder: (context, themeProvider, child) {
          return MaterialApp(
            title: AppConfig.appName,
            debugShowCheckedModeBanner: false,
            theme: AppTheme.lightTheme,
            darkTheme: AppTheme.darkTheme,
            themeMode: themeProvider.themeMode,
            home: const AuthWrapper(),
          );
        },
      ),
    );
  }
}

class AuthWrapper extends StatefulWidget {
  const AuthWrapper({super.key});

  @override
  State<AuthWrapper> createState() => _AuthWrapperState();
}

class _AuthWrapperState extends State<AuthWrapper> with WidgetsBindingObserver {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    Provider.of<AuthProvider>(context, listen: false).tryAutoLogin();
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      // Optional: Check time elapsed since paused
      // For high security, we lock immediately
      Provider.of<SecurityProvider>(context, listen: false).lockApp();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer2<AuthProvider, SecurityProvider>(
      builder: (ctx, auth, security, _) {
        if (!auth.isAuthenticated) {
          return const LoginScreen();
        }

        if (security.isPinEnabled && !security.isAuthenticated) {
          return PinScreen(
            mode: PinMode.verify,
            onSuccess: () {
              // The provider is updated inside the PinScreen via verifyPin,
              // but we can also set it explicitly if needed, though verifyPin does it.
              // Actually PinScreen calls widget.onSuccess, but we need to make sure state is updated.
              // In PinScreen _handlePinComplete: securityProvider.verifyPin(_pin) sets _isAuthenticated = true;
              // So we just need this widget to rebuild, which Consumer2 does.
            },
          );
        }

        return const MainNavigationScreen();
      },
    );
  }
}
