class User {
  final int id;
  final String name;
  final String email;
  final String role;
  final String roleLabel;
  final String? department;
  final String? position;
  final String? rank;
  final String? avatarUrl;
  final String? signatureUrl;
  final Map<String, dynamic>? supervisor;
  final Map<String, dynamic>? manager;

  User({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    required this.roleLabel,
    this.department,
    this.position,
    this.rank,
    this.avatarUrl,
    this.signatureUrl,
    this.supervisor,
    this.manager,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      role: json['role'] ?? 'employee',
      roleLabel: json['role_label'] ?? '',
      department: json['department'],
      position: json['position'],
      rank: json['rank'],
      avatarUrl: json['avatar_url'],
      signatureUrl: json['signature_url'],
      supervisor: json['supervisor'] is Map ? Map<String, dynamic>.from(json['supervisor']) : null,
      manager: json['manager'] is Map ? Map<String, dynamic>.from(json['manager']) : null,
    );
  }

  String get displayName => rank != null && rank!.isNotEmpty ? '$rank $name' : name;

  bool get isAdmin => role == 'admin';
  bool get isDirector => role == 'director';
  bool get isDeputyDirector => role == 'deputy_director';
  bool get isDepartmentHead => role == 'department_head';
  bool get isSupervisor => role == 'supervisor';
  bool get canApprove => ['admin', 'director', 'deputy_director', 'department_head', 'supervisor'].contains(role);
}
