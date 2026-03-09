class GuardChangeRequest {
  final int id;
  final int userId;
  final int replacementUserId;
  final String dutyPosition;
  final String dutyDate;
  final String? remarks;
  final String status;
  final int? approverId;
  final String? approvalComment;
  final String? approvedAt;
  final int? directorApproverId;
  final String? directorComment;
  final String? directorApprovedAt;
  final int? finalApproverId;
  final String? finalComment;
  final String? finalApprovedAt;
  final Map<String, dynamic>? user;
  final Map<String, dynamic>? replacementUser;
  final Map<String, dynamic>? approver;
  final Map<String, dynamic>? directorApprover;
  final Map<String, dynamic>? finalApprover;
  final String? createdAt;

  GuardChangeRequest({
    required this.id,
    required this.userId,
    required this.replacementUserId,
    required this.dutyPosition,
    required this.dutyDate,
    this.remarks,
    required this.status,
    this.approverId,
    this.approvalComment,
    this.approvedAt,
    this.directorApproverId,
    this.directorComment,
    this.directorApprovedAt,
    this.finalApproverId,
    this.finalComment,
    this.finalApprovedAt,
    this.user,
    this.replacementUser,
    this.approver,
    this.directorApprover,
    this.finalApprover,
    this.createdAt,
  });

  factory GuardChangeRequest.fromJson(Map<String, dynamic> json) {
    return GuardChangeRequest(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? 0,
      replacementUserId: json['replacement_user_id'] ?? 0,
      dutyPosition: json['duty_position'] ?? '',
      dutyDate: json['duty_date'] is String
          ? json['duty_date']
          : (json['duty_date'] ?? '').toString(),
      remarks: json['remarks'],
      status: json['status'] ?? '',
      approverId: json['approver_id'],
      approvalComment: json['approval_comment'],
      approvedAt: json['approved_at'],
      directorApproverId: json['director_approver_id'],
      directorComment: json['director_comment'],
      directorApprovedAt: json['director_approved_at'],
      finalApproverId: json['final_approver_id'],
      finalComment: json['final_comment'],
      finalApprovedAt: json['final_approved_at'],
      user: json['user'] is Map ? Map<String, dynamic>.from(json['user']) : null,
      replacementUser: json['replacement_user'] is Map ? Map<String, dynamic>.from(json['replacement_user']) : null,
      approver: json['approver'] is Map ? Map<String, dynamic>.from(json['approver']) : null,
      directorApprover: json['director_approver'] is Map ? Map<String, dynamic>.from(json['director_approver']) : null,
      finalApprover: json['final_approver'] is Map ? Map<String, dynamic>.from(json['final_approver']) : null,
      createdAt: json['created_at'],
    );
  }

  String get dutyPositionLabel {
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

  String get statusLabel {
    switch (status) {
      case 'pending':
        return 'รอการตอบรับ';
      case 'approved':
        return 'ตอบรับแล้ว';
      case 'director_approved':
        return 'รอง ผอ. อนุมัติ';
      case 'final_approved':
        return 'อนุมัติแล้ว';
      case 'rejected':
        return 'ปฏิเสธ';
      default:
        return status;
    }
  }

  bool get isPending => status == 'pending';
  bool get isApproved => status == 'approved' || status == 'director_approved' || status == 'final_approved';
  bool get isRejected => status == 'rejected';

  String get requesterName {
    if (user != null) {
      final rank = user!['rank'] ?? '';
      final name = user!['name'] ?? '';
      return rank.isNotEmpty ? '$rank $name' : name;
    }
    return '';
  }

  String get replacementName {
    if (replacementUser != null) {
      final rank = replacementUser!['rank'] ?? '';
      final name = replacementUser!['name'] ?? '';
      return rank.isNotEmpty ? '$rank $name' : name;
    }
    return '';
  }
}
