import 'leave_type.dart';

class LeaveBalance {
  final int id;
  final LeaveType? leaveType;
  final int year;
  final double totalDays;
  final double usedDays;
  final double remainingDays;

  LeaveBalance({
    required this.id,
    this.leaveType,
    required this.year,
    required this.totalDays,
    required this.usedDays,
    required this.remainingDays,
  });

  factory LeaveBalance.fromJson(Map<String, dynamic> json) {
    return LeaveBalance(
      id: json['id'] ?? 0,
      leaveType: json['leave_type'] != null ? LeaveType.fromJson(json['leave_type']) : null,
      year: json['year'] ?? DateTime.now().year,
      totalDays: (json['total_days'] ?? 0).toDouble(),
      usedDays: (json['used_days'] ?? 0).toDouble(),
      remainingDays: (json['remaining_days'] ?? 0).toDouble(),
    );
  }

  double get usagePercentage => totalDays > 0 ? (usedDays / totalDays) * 100 : 0;
}
