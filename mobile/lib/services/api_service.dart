import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../config/app_config.dart';

class ApiService {
  late Dio _dio;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  ApiService() {
    _dio = Dio(
      BaseOptions(
        baseUrl: AppConfig.baseUrl,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        connectTimeout: const Duration(seconds: 10),
        receiveTimeout: const Duration(seconds: 10),
      ),
    );

    // Add interceptor to inject token
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          final token = await _storage.read(key: 'token');
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          return handler.next(options);
        },
        onError: (DioException e, handler) {
          // Handle global errors (e.g. 401 Unauthorized)
          if (e.response?.statusCode == 401) {
            // TODO: Logout logic if needed
          }
          return handler.next(e);
        },
      ),
    );
  }

  Future<Response> login(String email, String password) async {
    return await _dio.post(
      '/login',
      data: {
        'email': email,
        'password': password,
        'device_name': 'flutter_app',
      },
    );
  }

  Future<void> logout() async {
    try {
      await _dio.post('/logout');
    } catch (e) {
      // Ignore errors during logout
    }
    await _storage.delete(key: 'token');
  }

  Future<Response> getUserProfile() async {
    return await _dio.get('/me');
  }

  // --- Leave Features ---

  Future<Response> getLeaveTypes() async {
    return await _dio.get('/leave-types');
  }

  Future<Response> getLeaveBalance() async {
    return await _dio.get('/leave-balance');
  }

  Future<Response> getLeaveRequests({String? status}) async {
    final Map<String, dynamic> query = {};
    if (status != null) query['status'] = status;
    return await _dio.get('/leave-requests', queryParameters: query);
  }

  Future<Response> submitLeaveRequest(Map<String, dynamic> data) async {
    return await _dio.post('/leave-requests', data: data);
  }

  Future<Response> cancelLeaveRequest(int id) async {
    return await _dio.post('/leave-requests/$id/cancel');
  }

  // --- Approvals ---

  Future<Response> getApprovals() async {
    return await _dio.get('/approvals');
  }

  Future<Response> approveRequest(
    int id, {
    String? comment,
    String? signature,
    bool useSavedSignature = false,
  }) async {
    return await _dio.post(
      '/approvals/$id/approve',
      data: {
        'comment': comment,
        'signature': signature,
        'use_saved_signature': useSavedSignature,
      },
    );
  }

  Future<Response> rejectRequest(int id, {String? comment}) async {
    return await _dio.post('/approvals/$id/reject', data: {'comment': comment});
  }

  Future<Response> updateFcmToken(String token) async {
    return await _dio.post('/fcm-token', data: {'token': token});
  }

  // Expose dio for other calls
  Dio get client => _dio;
}
