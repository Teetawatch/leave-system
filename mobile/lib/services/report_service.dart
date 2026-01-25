import 'api_service.dart';

class ReportService {
  final ApiService _apiService = ApiService();

  Future<Map<String, dynamic>> getLeaveSummary({
    String? startDate,
    String? endDate,
    String? department,
    String? status,
  }) async {
    final queryParams = <String, dynamic>{};
    if (startDate != null) queryParams['start_date'] = startDate;
    if (endDate != null) queryParams['end_date'] = endDate;
    if (department != null) queryParams['department'] = department;
    if (status != null) queryParams['status'] = status;

    final response = await _apiService.client.get(
      '/reports/leave-summary',
      queryParameters: queryParams,
    );

    if (response.statusCode == 200) {
      return response.data;
    } else {
      throw Exception('Failed to load leave summary');
    }
  }

  Future<Map<String, dynamic>> getGuardChangeSummary({
    String? startDate,
    String? endDate,
    String? department,
    String? status,
  }) async {
    final queryParams = <String, dynamic>{};
    if (startDate != null) queryParams['start_date'] = startDate;
    if (endDate != null) queryParams['end_date'] = endDate;
    if (department != null) queryParams['department'] = department;
    if (status != null) queryParams['status'] = status;

    final response = await _apiService.client.get(
      '/reports/guard-change',
      queryParameters: queryParams,
    );

    if (response.statusCode == 200) {
      return response.data;
    } else {
      throw Exception('Failed to load guard change summary');
    }
  }
}
