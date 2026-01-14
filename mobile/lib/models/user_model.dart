class User {
  final int id;
  final String name;
  final String email;
  final String role;
  final String roleLabel;
  final String department;
  final String? position;
  final String? avatarUrl;

  User({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    required this.roleLabel,
    required this.department,
    this.position,
    this.avatarUrl,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      role: json['role'],
      roleLabel: json['role_label'] ?? json['role'],
      department: json['department'],
      position: json['position'],
      avatarUrl: json['avatar_url'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
      'role_label': roleLabel,
      'department': department,
      'position': position,
      'avatar_url': avatarUrl,
    };
  }
}
