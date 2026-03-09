import 'package:flutter/foundation.dart';
import '../config/api_config.dart';
import '../models/duty_roster.dart';
import '../services/api_service.dart';

class GuardDutyService {
  static final ApiService _api = ApiService();

  /// Get today's guard duty assignments
  static Future<List<Map<String, dynamic>>> getTodayGuardDuty() async {
    try {
      final response = await _api.get(ApiConfig.todayGuardDuty);
      if (response['success'] == true && response['data'] is List) {
        return (response['data'] as List)
            .map((g) => Map<String, dynamic>.from(g))
            .toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching today guard duty: $e');
      return [];
    }
  }

  /// Get guard duty for a specific date (YYYY-MM-DD)
  static Future<List<TodayGuardEntry>> getByDate(String date) async {
    try {
      final response = await _api.get(
        ApiConfig.dutyRoster,
        queryParams: {'date': date},
      );
      if (response['success'] == true && response['data'] is List) {
        return (response['data'] as List)
            .map((g) => TodayGuardEntry.fromJson(g))
            .toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching duty roster by date: $e');
      return [];
    }
  }

  /// Get monthly duty roster (?year=YYYY&month=MM)
  static Future<List<DutyRosterDay>> getMonthly(int year, int month) async {
    try {
      final response = await _api.get(
        ApiConfig.dutyRosterMonthly,
        queryParams: {'year': '$year', 'month': '$month'},
      );
      if (response['success'] == true && response['data'] is List) {
        return (response['data'] as List)
            .map((d) => DutyRosterDay.fromJson(d))
            .toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching monthly duty roster: $e');
      return [];
    }
  }
}
