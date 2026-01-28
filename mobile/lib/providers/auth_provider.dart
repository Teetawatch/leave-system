import 'package:flutter/material.dart';
import 'package:dio/dio.dart' as dio;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';
import '../services/notification_service.dart';

class AuthProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  User? _user;
  bool _isLoading = false;

  User? get user => _user;
  bool get isLoading => _isLoading;
  bool get isAuthenticated => _user != null;

  // Try to restore session on app start
  Future<void> tryAutoLogin() async {
    final token = await _storage.read(key: 'token');
    if (token == null) return;

    try {
      final response = await _apiService.getUserProfile();
      if (response.data['success']) {
        _user = User.fromJson(
          response.data['data']['user'],
        ); // Modified this line

        // Update FCM Token
        try {
          print('DEBUG: Attempting to get FCM Token...');
          final fcmToken = await NotificationService().getToken();
          print('DEBUG: FCM Token retrieved: $fcmToken');

          if (fcmToken != null) {
            print('DEBUG: Sending token to server...');
            await _apiService.updateFcmToken(fcmToken);
            print('DEBUG: Token sent successfully');
          } else {
            print('DEBUG: Token is null');
          }
        } catch (e) {
          print('DEBUG: Error updating FCM token: $e');
        }

        notifyListeners();
      }
    } catch (e) {
      // Token invalid or network error
      await logout();
    }
  }

  Future<String?> login(String email, String password) async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await _apiService.login(email, password);

      if (response.data['success']) {
        final token = response.data['data']['token'];
        await _storage.write(key: 'token', value: token);

        _user = User.fromJson(response.data['data']['user']);

        // Update FCM Token immediately after login
        try {
          final fcmToken = await NotificationService().getToken();
          if (fcmToken != null) {
            await _apiService.updateFcmToken(fcmToken);
          }
        } catch (e) {
          print('Error updating FCM token after login: $e');
        }

        _isLoading = false;
        notifyListeners();
        return null; // Success
      }
    } catch (e) {
      String errorMessage = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
      if (e is dio.DioException) {
        print('Login Status Code: ${e.response?.statusCode}');
        print('Login Response Data: ${e.response?.data}');

        if (e.response?.statusCode == 422) {
          final data = e.response?.data;
          if (data != null && data['message'] != null) {
            errorMessage = data['message'];
          } else if (data != null && data['errors'] != null) {
            // Extract first error
            final errors = data['errors'] as Map<String, dynamic>;
            errorMessage = errors.values.first[0];
          }
        } else {
          errorMessage = 'Server Error: ${e.response?.statusCode}';
        }
      } else {
        errorMessage = 'Connection Error: $e';
      }

      _isLoading = false;
      notifyListeners();
      return errorMessage;
    }

    _isLoading = false;
    notifyListeners();
    return 'เกิดข้อผิดพลาดในการเข้าสู่ระบบ';
  }

  Future<void> logout() async {
    try {
      await _apiService.logout();
    } catch (e) {
      print('Logout Error: $e');
    } finally {
      // Always clear user and update UI
      await _storage.delete(key: 'token');
      _user = null;
      notifyListeners();
    }
  }

  Future<bool> updateProfile({
    String? avatarPath,
    String? signaturePath,
  }) async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await _apiService.updateProfile(
        avatarPath: avatarPath,
        signaturePath: signaturePath,
      );

      if (response.data['success']) {
        _user = User.fromJson(response.data['data']['user']);
        _isLoading = false;
        notifyListeners();
        return true;
      }
    } catch (e) {
      print('Update Profile Error: $e');
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }
}
