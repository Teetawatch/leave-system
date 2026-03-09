import 'package:flutter/material.dart';
import '../config/api_config.dart';
import '../models/user.dart';
import '../services/api_service.dart';

class AuthProvider extends ChangeNotifier {
  final ApiService _api = ApiService();
  User? _user;
  bool _isLoading = false;
  bool _isLoggedIn = false;
  String? _error;

  User? get user => _user;
  bool get isLoading => _isLoading;
  bool get isLoggedIn => _isLoggedIn;
  String? get error => _error;

  Future<bool> checkAuth() async {
    final token = await _api.getToken();
    if (token == null) return false;

    try {
      _isLoading = true;
      notifyListeners();

      final response = await _api.get(ApiConfig.me);
      if (response['success'] == true) {
        _user = User.fromJson(response['data']['user']);
        _isLoggedIn = true;
        _isLoading = false;
        notifyListeners();
        return true;
      }
    } catch (e) {
      await _api.deleteToken();
    }

    _isLoading = false;
    _isLoggedIn = false;
    notifyListeners();
    return false;
  }

  Future<bool> login(String email, String password) async {
    try {
      _isLoading = true;
      _error = null;
      notifyListeners();

      final response = await _api.postNoAuth(ApiConfig.login, body: {
        'email': email,
        'password': password,
        'device_name': 'flutter_app',
      });

      if (response['success'] == true) {
        final token = response['data']['token'];
        await _api.saveToken(token);
        _user = User.fromJson(response['data']['user']);
        _isLoggedIn = true;
        _isLoading = false;
        notifyListeners();
        return true;
      }

      _error = 'เข้าสู่ระบบไม่สำเร็จ';
    } on ApiException catch (e) {
      _error = e.message;
    } catch (e) {
      _error = 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้';
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }

  Future<void> logout() async {
    try {
      await _api.post(ApiConfig.logout);
    } catch (_) {}
    await _api.deleteToken();
    _user = null;
    _isLoggedIn = false;
    notifyListeners();
  }

  Future<void> refreshUser() async {
    try {
      final response = await _api.get(ApiConfig.me);
      if (response['success'] == true) {
        _user = User.fromJson(response['data']['user']);
        notifyListeners();
      }
    } catch (_) {}
  }
}
