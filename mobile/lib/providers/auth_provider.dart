import 'package:flutter/material.dart';
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

  Future<bool> login(String email, String password) async {
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
        return true;
      }
    } catch (e) {
      print('Login Error: $e');
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }

  Future<void> logout() async {
    await _apiService.logout();
    _user = null;
    notifyListeners();
  }
}
