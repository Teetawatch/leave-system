import 'user.dart';
import 'leave_type.dart';
import 'approval.dart';

class LeaveRequest {
  final int id;
  final User? user;
  final LeaveType? leaveType;
  final String startDate;
  final String endDate;
  final double totalDays;
  final String reason;
  final dynamic contactAddress;
  final String status;
  final String statusLabel;
  final String? attachmentUrl;
  final List<Approval> approvals;
  final String createdAt;
  final String updatedAt;
  final String? cancelledAt;

  LeaveRequest({
    required this.id,
    this.user,
    this.leaveType,
    required this.startDate,
    required this.endDate,
    required this.totalDays,
    required this.reason,
    this.contactAddress,
    required this.status,
    required this.statusLabel,
    this.attachmentUrl,
    this.approvals = const [],
    required this.createdAt,
    required this.updatedAt,
    this.cancelledAt,
  });

  factory LeaveRequest.fromJson(Map<String, dynamic> json) {
    return LeaveRequest(
      id: json['id'] ?? 0,
      user: json['user'] != null ? User.fromJson(json['user']) : null,
      leaveType: json['leave_type'] != null ? LeaveType.fromJson(json['leave_type']) : null,
      startDate: json['start_date'] ?? '',
      endDate: json['end_date'] ?? '',
      totalDays: (json['total_days'] ?? 0).toDouble(),
      reason: json['reason'] ?? '',
      contactAddress: json['contact_address'],
      status: json['status'] ?? '',
      statusLabel: json['status_label'] ?? '',
      attachmentUrl: json['attachment_url'],
      approvals: json['approvals'] != null
          ? (json['approvals'] as List).map((a) => Approval.fromJson(a)).toList()
          : [],
      createdAt: json['created_at'] ?? '',
      updatedAt: json['updated_at'] ?? '',
      cancelledAt: json['cancelled_at'],
    );
  }

  bool get isPending => status.startsWith('pending');
  bool get isApproved => status == 'approved';
  bool get isRejected => status == 'rejected';
  bool get isCancelled => status == 'cancelled';
  bool get canCancel => ['pending_supervisor', 'pending_head', 'pending_manager'].contains(status);
}
