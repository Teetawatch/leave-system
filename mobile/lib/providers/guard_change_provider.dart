import 'package:flutter/material.dart';
import '../models/guard_change_model.dart';
import '../services/api_service.dart';
import 'package:dio/dio.dart';

class GuardChangeProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();

  List<GuardChangeRequest> _myRequests = [];
  List<GuardChangeRequest> _approvals = [];
  bool _isLoading = false;

  List<GuardChangeRequest> get myRequests => _myRequests;
  List<GuardChangeRequest> get approvals => _approvals;
  bool get isLoading => _isLoading;

  Future<void> fetchMyRequests() async {
    _isLoading = true;
    notifyListeners();
    try {
      final response = await _apiService.getGuardChangeRequests();
      if (response.data['success']) {
        final List list = response.data['data'];
        _myRequests = list.map((e) => GuardChangeRequest.fromJson(e)).toList();
      }
    } catch (e) {
      debugPrint('Error fetching my guard change requests: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchApprovals() async {
    _isLoading = true;
    notifyListeners();
    try {
      final response = await _apiService.getGuardChangeApprovals();
      if (response.data['success']) {
        final List list = response.data['data'];
        _approvals = list.map((e) => GuardChangeRequest.fromJson(e)).toList();
      }
    } catch (e) {
      debugPrint('Error fetching guard change approvals: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> submitRequest(Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();
    try {
      final response = await _apiService.submitGuardChangeRequest(data);
      if (response.data['success']) {
        await fetchMyRequests();
        return true;
      }
      return false;
    } catch (e) {
      debugPrint('Error submitting guard change request: $e');
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> approveRequest(
    int id, {
    String? comment,
    String? signature,
    bool useSavedSignature = false,
  }) async {
    _isLoading = true;
    notifyListeners();
    try {
      final response = await _apiService.approveGuardChange(
        id,
        comment: comment,
        signature: signature,
        useSavedSignature: useSavedSignature,
      );
      if (response.data['success']) {
        await fetchApprovals();
        return true;
      }
      return false;
    } catch (e) {
      debugPrint('Error approving guard change: $e');
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> rejectRequest(int id, {String? comment}) async {
    _isLoading = true;
    notifyListeners();
    try {
      final response = await _apiService.rejectGuardChange(
        id,
        comment: comment,
      );
      if (response.data['success']) {
        await fetchApprovals();
        return true;
      }
      return false;
    } catch (e) {
      debugPrint('Error rejecting guard change: $e');
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<Map<String, dynamic>> cancelRequest(int id) async {
    _isLoading = true;
    notifyListeners();
    try {
      final response = await _apiService.cancelGuardChangeRequest(id);
      if (response.data['success']) {
        await fetchMyRequests();
        return {
          'success': true,
          'message': response.data['message'] ?? 'ยกเลิกคำขอเรียบร้อยแล้ว',
        };
      }
      return {
        'success': false,
        'message': response.data['message'] ?? 'ไม่สามารถยกเลิกได้',
      };
    } catch (e) {
      debugPrint('Error canceling guard change request: $e');
      if (e is DioException && e.response?.data != null) {
        return {
          'success': false,
          'message':
              e.response!.data['message'] ?? 'เกิดข้อผิดพลาดในการเชื่อมต่อ',
        };
      }
      return {'success': false, 'message': 'เกิดข้อผิดพลาด: $e'};
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
