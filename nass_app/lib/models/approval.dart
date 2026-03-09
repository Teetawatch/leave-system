import 'user.dart';

class Approval {
  final int id;
  final User? approver;
  final String step;
  final String action;
  final String? comment;
  final String? signature;
  final String createdAt;

  Approval({
    required this.id,
    this.approver,
    required this.step,
    required this.action,
    this.comment,
    this.signature,
    required this.createdAt,
  });

  factory Approval.fromJson(Map<String, dynamic> json) {
    return Approval(
      id: json['id'] ?? 0,
      approver: json['approver'] != null ? User.fromJson(json['approver']) : null,
      step: json['step'] ?? '',
      action: json['action'] ?? '',
      comment: json['comment'],
      signature: json['signature'],
      createdAt: json['created_at'] ?? '',
    );
  }

  bool get isApproved => action == 'approved';
  bool get isRejected => action == 'rejected';
  bool get isAcknowledged => action == 'acknowledged';
}
