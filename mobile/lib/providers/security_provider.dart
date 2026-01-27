import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:local_auth/local_auth.dart' as local_auth;
import 'package:shared_preferences/shared_preferences.dart';

class SecurityProvider with ChangeNotifier {
  static const String keyPinEnabled = 'pin_enabled';
  static const String keyBiometricEnabled = 'biometric_enabled';
  static const String keyPinCode = 'pin_code';

  final FlutterSecureStorage _storage = const FlutterSecureStorage();
  final local_auth.LocalAuthentication _localAuth =
      local_auth.LocalAuthentication();

  bool _isPinEnabled = false;
  bool _isBiometricEnabled = false;
  bool _isAuthenticated = false;

  bool get isPinEnabled => _isPinEnabled;
  bool get isBiometricEnabled => _isBiometricEnabled;
  bool get isAuthenticated => _isAuthenticated;

  SecurityProvider() {
    _loadSettings();
  }

  Future<void> _loadSettings() async {
    final prefs = await SharedPreferences.getInstance();
    _isPinEnabled = prefs.getBool(keyPinEnabled) ?? false;
    _isBiometricEnabled = prefs.getBool(keyBiometricEnabled) ?? false;

    // If security is enabled, we need authentication initially
    // Unless it's the very first run, but logically if enabled, we are locked.
    // However, on app restart, we want to lock if enabled.
    if (_isPinEnabled || _isBiometricEnabled) {
      _isAuthenticated = false;
    } else {
      _isAuthenticated = true; // No security needed
    }
    notifyListeners();
  }

  void setAuthenticated(bool value) {
    _isAuthenticated = value;
    notifyListeners();
  }

  Future<bool> checkHasPin() async {
    String? pin = await _storage.read(key: keyPinCode);
    return pin != null && pin.isNotEmpty;
  }

  Future<void> setPin(String pin) async {
    await _storage.write(key: keyPinCode, value: pin);
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(keyPinEnabled, true);
    _isPinEnabled = true;
    notifyListeners();
  }

  Future<void> removePin() async {
    await _storage.delete(key: keyPinCode);
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(keyPinEnabled, false);
    await prefs.setBool(
      keyBiometricEnabled,
      false,
    ); // Disable bio if pin removed
    _isPinEnabled = false;
    _isBiometricEnabled = false;
    notifyListeners(); // Keep authenticated true as user just removed it
  }

  Future<bool> verifyPin(String inputPin) async {
    String? storedPin = await _storage.read(key: keyPinCode);
    if (storedPin == inputPin) {
      _isAuthenticated = true;
      notifyListeners();
      return true;
    }
    return false;
  }

  Future<void> toggleBiometric(bool enabled) async {
    final prefs = await SharedPreferences.getInstance();
    if (enabled) {
      bool canCheckBiometrics = await _localAuth.canCheckBiometrics;
      if (canCheckBiometrics) {
        bool authenticated = await _localAuth.authenticate(
          localizedReason: 'โปรดยืนยันตัวตนเพื่อเปิดใช้งาน Biometric',
          options: const local_auth.AuthenticationOptions(
            biometricOnly: true,
            stickyAuth: true,
          ),
        );
        if (authenticated) {
          await prefs.setBool(keyBiometricEnabled, true);
          _isBiometricEnabled = true;
        }
      } else {
        throw PlatformException(
          code: 'NO_BIOMETRICS',
          message: 'อุปกรณ์ไม่รองรับ Biometrics',
        );
      }
    } else {
      await prefs.setBool(keyBiometricEnabled, false);
      _isBiometricEnabled = false;
    }
    notifyListeners();
  }

  Future<bool> authenticateWithBiometrics() async {
    if (!_isBiometricEnabled) return false;
    try {
      bool authenticated = await _localAuth.authenticate(
        localizedReason: 'โปรดยืนยันตัวตนเพื่อเข้าใช้งาน',
        options: const local_auth.AuthenticationOptions(
          biometricOnly: true,
          stickyAuth: true,
        ),
      );
      if (authenticated) {
        _isAuthenticated = true;
        notifyListeners();
      }
      return authenticated;
    } catch (e) {
      debugPrint('Biometric error: $e');
      return false;
    }
  }
}
