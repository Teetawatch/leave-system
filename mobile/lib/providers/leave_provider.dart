import 'package:flutter/material.dart';
import '../models/leave_balance_model.dart';
import '../models/leave_request_model.dart';
import '../models/leave_type_model.dart';
import '../services/api_service.dart';
import 'package:dio/dio.dart'; // Import Request for proper type check

class LeaveProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();

  List<LeaveBalance> _balances = [];
  List<LeaveType> _leaveTypes = [];
  List<LeaveRequest> _myRequests = [];
  List<LeaveRequest> _pendingApprovals = [];
  bool _isLoading = false;

  List<LeaveBalance> get balances => _balances;
  List<LeaveType> get leaveTypes => _leaveTypes;
  List<LeaveRequest> get myRequests => _myRequests;
  List<LeaveRequest> get pendingApprovals => _pendingApprovals;
  bool get isLoading => _isLoading;

  Future<void> fetchLeaveBalances() async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await _apiService.getLeaveBalance();
      if (response.data['success']) {
        final List balData = response.data['data']['balances'];
        _balances = balData.map((json) => LeaveBalance.fromJson(json)).toList();
      }
    } catch (e) {
      debugPrint('Error fetching balances: $e');
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> fetchLeaveTypes() async {
    try {
      final response = await _apiService.getLeaveTypes();
      if (response.data['success']) {
        final List typeData = response.data['data'];
        _leaveTypes = typeData.map((json) => LeaveType.fromJson(json)).toList();
      }
    } catch (e) {
      debugPrint('Error fetching leave types: $e');
    }
    notifyListeners();
  }

  Future<void> fetchMyRequests() async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await _apiService.getLeaveRequests();
      if (response.data['success']) {
        final List reqData = response.data['data'];
        _myRequests = reqData
            .map((json) => LeaveRequest.fromJson(json))
            .toList();
      }
    } catch (e) {
      debugPrint('Error fetching my requests: $e');
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> fetchPendingApprovals() async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await _apiService.getApprovals();
      if (response.data['success']) {
        final List appData = response.data['data'];
        _pendingApprovals = appData
            .map((json) => LeaveRequest.fromJson(json))
            .toList();
      }
    } catch (e) {
      debugPrint('Error fetching approvals: $e');
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<Map<String, dynamic>> submitRequest(
    Map<String, dynamic> data, {
    String? attachmentPath,
  }) async {
    try {
      final response = await _apiService.submitLeaveRequest(
        data,
        attachmentPath: attachmentPath,
      );
      if (response.data['success']) {
        await fetchMyRequests();
        await fetchLeaveBalances();
        return {
          'success': true,
          'message': response.data['message'] ?? 'สำเร็จ',
        };
      }
      return {
        'success': false,
        'message': response.data['message'] ?? 'เกิดข้อผิดพลาด',
      };
    } catch (e) {
      debugPrint('Error submitting request: $e');
      if (e is DioException && e.response?.data != null) {
        return {
          'success': false,
          'message':
              e.response!.data['message'] ?? 'เกิดข้อผิดพลาดในการเชื่อมต่อ',
        };
      }
      return {'success': false, 'message': 'เกิดข้อผิดพลาด: $e'};
    }
  }

  Future<Map<String, dynamic>> approveRequest(
    int id, {
    String? comment,
    String? signature,
    bool useSavedSignature = false,
  }) async {
    try {
      final response = await _apiService.approveRequest(
        id,
        comment: comment,
        signature: signature,
        useSavedSignature: useSavedSignature,
      );
      if (response.data['success']) {
        await fetchPendingApprovals();
        return {
          'success': true,
          'message': response.data['message'] ?? 'อนุมัติเรียบร้อยแล้ว',
        };
      }
      return {
        'success': false,
        'message': response.data['message'] ?? 'ไม่สามารถอนุมัติได้',
      };
    } catch (e) {
      debugPrint('Error approving request: $e');
      if (e is DioException && e.response?.data != null) {
        return {
          'success': false,
          'message':
              e.response!.data['message'] ?? 'เกิดข้อผิดพลาดในการเชื่อมต่อ',
        };
      }
      return {'success': false, 'message': 'เกิดข้อผิดพลาด: $e'};
    }
  }

  Future<Map<String, dynamic>> rejectRequest(int id, {String? comment}) async {
    try {
      final response = await _apiService.rejectRequest(id, comment: comment);
      if (response.data['success']) {
        await fetchPendingApprovals();
        return {
          'success': true,
          'message': response.data['message'] ?? 'ปฏิเสธเรียบร้อยแล้ว',
        };
      }
      return {
        'success': false,
        'message': response.data['message'] ?? 'ไม่สามารถปฏิเสธได้',
      };
    } catch (e) {
      debugPrint('Error rejecting request: $e');
      if (e is DioException && e.response?.data != null) {
        return {
          'success': false,
          'message':
              e.response!.data['message'] ?? 'เกิดข้อผิดพลาดในการเชื่อมต่อ',
        };
      }
      return {'success': false, 'message': 'เกิดข้อผิดพลาด: $e'};
    }
  }

  Future<Map<String, dynamic>> cancelRequest(int id) async {
    try {
      final response = await _apiService.cancelLeaveRequest(id);
      if (response.data['success']) {
        await fetchMyRequests();
        await fetchLeaveBalances();
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
      debugPrint('Error canceling request: $e');
      if (e is DioException && e.response?.data != null) {
        return {
          'success': false,
          'message':
              e.response!.data['message'] ?? 'เกิดข้อผิดพลาดในการเชื่อมต่อ',
        };
      }
      return {'success': false, 'message': 'เกิดข้อผิดพลาด: $e'};
    }
  }
}
