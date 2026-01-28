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
        'device_name': 'LeaveSystemMobile',
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

  Future<Response> getAllUsers() async {
    return await _dio.get('/users');
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

  Future<Response> submitLeaveRequest(
    Map<String, dynamic> data, {
    String? attachmentPath,
  }) async {
    if (attachmentPath != null) {
      final Map<String, dynamic> formDataMap = Map.from(data);
      formDataMap['attachment'] = await MultipartFile.fromFile(attachmentPath);
      final formData = FormData.fromMap(formDataMap);
      return await _dio.post('/leave-requests', data: formData);
    }
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

  // --- Guard Change Features ---

  Future<Response> getGuardChangeRequests() async {
    return await _dio.get('/guard-change-requests');
  }

  Future<Response> submitGuardChangeRequest(Map<String, dynamic> data) async {
    return await _dio.post('/guard-change-requests', data: data);
  }

  Future<Response> cancelGuardChangeRequest(int id) async {
    return await _dio.post('/guard-change-requests/$id/cancel');
  }

  Future<Response> getGuardChangeApprovals() async {
    return await _dio.get('/guard-change-approvals');
  }

  Future<Response> approveGuardChange(
    int id, {
    String? comment,
    String? signature,
    bool useSavedSignature = false,
  }) async {
    return await _dio.post(
      '/guard-change-approvals/$id/approve',
      data: {
        'comment': comment,
        'signature': signature,
        'use_saved_signature': useSavedSignature ? '1' : '0',
      },
    );
  }

  Future<Response> rejectGuardChange(int id, {String? comment}) async {
    return await _dio.post(
      '/guard-change-approvals/$id/reject',
      data: {'comment': comment},
    );
  }

  Future<Response> getGuardChangeUsers() async {
    return await _dio.get('/guard-change-requests/users');
  }

  Future<Response> updateFcmToken(String token) async {
    return await _dio.post('/fcm-token', data: {'token': token});
  }

  Future<Response> updateProfile({
    String? avatarPath,
    String? signaturePath,
  }) async {
    final Map<String, dynamic> data = {};
    if (avatarPath != null) {
      data['avatar'] = await MultipartFile.fromFile(avatarPath);
    }
    if (signaturePath != null) {
      data['signature'] = await MultipartFile.fromFile(signaturePath);
    }

    FormData formData = FormData.fromMap(data);

    return await _dio.post('/profile', data: formData);
  }

  Future<Response> getLatestNews() async {
    return await Dio().get(
      'https://nass.ac.th/wp-json/wp/v2/posts?per_page=6&_embed&categories=5',
    );
  }

  // Expose dio for other calls
  Dio get client => _dio;
}
