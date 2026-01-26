import 'user_model.dart';
import 'package:intl/intl.dart';

class GuardChangeRequest {
  final int id;
  final int userId;
  final int replacementUserId;
  final String dutyPosition;
  final String dutyDate;
  final String? remarks;
  final String status;
  final int? approverId;
  final String? approvalSignature;
  final String? approvalComment;
  final String? approvedAt;
  final int? directorApproverId;
  final String? directorSignature;
  final String? directorComment;
  final String? directorApprovedAt;
  final int? finalApproverId;
  final String? finalSignature;
  final String? finalComment;
  final String? finalApprovedAt;
  final String createdAt;
  final User? user;
  final User? replacementUser;
  final User? approver;

  GuardChangeRequest({
    required this.id,
    required this.userId,
    required this.replacementUserId,
    required this.dutyPosition,
    required this.dutyDate,
    this.remarks,
    required this.status,
    this.approverId,
    this.approvalSignature,
    this.approvalComment,
    this.approvedAt,
    this.directorApproverId,
    this.directorSignature,
    this.directorComment,
    this.directorApprovedAt,
    this.finalApproverId,
    this.finalSignature,
    this.finalComment,
    this.finalApprovedAt,
    required this.createdAt,
    this.user,
    this.replacementUser,
    this.approver,
  });

  factory GuardChangeRequest.fromJson(Map<String, dynamic> json) {
    return GuardChangeRequest(
      id: json['id'],
      userId: json['user_id'],
      replacementUserId: json['replacement_user_id'],
      dutyPosition: json['duty_position'],
      dutyDate: json['duty_date'],
      remarks: json['remarks'],
      status: json['status'],
      approverId: json['approver_id'],
      approvalSignature: json['approval_signature'],
      approvalComment: json['approval_comment'],
      approvedAt: json['approved_at'],
      directorApproverId: json['director_approver_id'],
      directorSignature: json['director_signature'],
      directorComment: json['director_comment'],
      directorApprovedAt: json['director_approved_at'],
      finalApproverId: json['final_approver_id'],
      finalSignature: json['final_signature'],
      finalComment: json['final_comment'],
      finalApprovedAt: json['final_approved_at'],
      createdAt: json['created_at'],
      user: json['user'] != null ? User.fromJson(json['user']) : null,
      replacementUser: json['replacement_user'] != null
          ? User.fromJson(json['replacement_user'])
          : null,
      approver: json['approver'] != null
          ? User.fromJson(json['approver'])
          : null,
    );
  }

  String get formattedDutyDate {
    try {
      final date = DateTime.parse(dutyDate);
      return DateFormat('dd MMM yyyy').format(date);
    } catch (e) {
      return dutyDate;
    }
  }

  String get formattedCreatedAt {
    try {
      final date = DateTime.parse(createdAt);
      return DateFormat('dd/MM/yy HH:mm').format(date);
    } catch (e) {
      return createdAt;
    }
  }

  String get dutyPositionThai {
    switch (dutyPosition) {
      case 'senior_duty_officer':
        return 'นายทหารเวรอาวุโส';
      case 'duty_officer':
        return 'นายทหารเวร';
      case 'assistant_duty_officer':
        return 'ผู้ช่วยนายทหารเวร';
      default:
        return dutyPosition;
    }
  }
}
