import 'leave_type_model.dart';

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
      id: json['id'],
      leaveType: json['leave_type'] != null
          ? LeaveType.fromJson(json['leave_type'])
          : null,
      year: json['year'],
      totalDays: (json['total_days'] as num).toDouble(),
      usedDays: (json['used_days'] as num).toDouble(),
      remainingDays: (json['remaining_days'] as num).toDouble(),
    );
  }
}
