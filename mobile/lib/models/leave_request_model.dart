import 'leave_type_model.dart';
import 'user_model.dart';

class LeaveRequest {
  final int id;
  final LeaveType leaveType;
  final User? user; // Optional, useful for approvals list
  final DateTime startDate;
  final DateTime endDate;
  final int totalDays;
  final String reason;
  final String status;
  final String statusLabel; // e.g. "รอหัวหน้างาน"
  final DateTime createdAt;

  LeaveRequest({
    required this.id,
    required this.leaveType,
    this.user,
    required this.startDate,
    required this.endDate,
    required this.totalDays,
    required this.reason,
    required this.status,
    required this.statusLabel,
    required this.createdAt,
  });

  factory LeaveRequest.fromJson(Map<String, dynamic> json) {
    return LeaveRequest(
      id: json['id'],
      leaveType: LeaveType.fromJson(json['leave_type']),
      user: json['user'] != null ? User.fromJson(json['user']) : null,
      startDate: DateTime.parse(json['start_date']),
      endDate: DateTime.parse(json['end_date']),
      totalDays: (json['total_days'] is int)
          ? json['total_days']
          : (json['total_days'] as double).round(),
      reason: json['reason'],
      status: json['status'],
      statusLabel: json['status_label'] ?? json['status'],
      createdAt: DateTime.parse(json['created_at']),
    );
  }

  String get formattedStartDate =>
      "${startDate.day}/${startDate.month}/${startDate.year + 543}";
  String get formattedEndDate =>
      "${endDate.day}/${endDate.month}/${endDate.year + 543}";
  String get formattedCreatedAt =>
      "${createdAt.day}/${createdAt.month}/${createdAt.year + 543}";
}
