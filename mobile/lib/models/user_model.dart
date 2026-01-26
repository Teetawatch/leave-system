class User {
  final int id;
  final String name;
  final String email;
  final String role;
  final String roleLabel;
  final String department;
  final String? position;
  final String? rank;
  final String? avatarUrl;
  final String? signatureUrl;

  User({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    required this.roleLabel,
    required this.department,
    this.position,
    this.rank,
    this.avatarUrl,
    this.signatureUrl,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    String? avatar = json['avatar_url'];
    String? signature = json['signature_url'];

    // Fix for Android Emulator to access localhost
    if (avatar != null && avatar.contains('localhost')) {
      avatar = avatar.replaceFirst('localhost', '10.0.2.2');
    }
    if (signature != null && signature.contains('localhost')) {
      signature = signature.replaceFirst('localhost', '10.0.2.2');
    }

    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      role: json['role'],
      roleLabel: json['role_label'] ?? json['role'],
      department: json['department'],
      position: json['position'],
      rank: json['rank'],
      avatarUrl: avatar,
      signatureUrl: signature,
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
      'rank': rank,
      'avatar_url': avatarUrl,
      'signature_url': signatureUrl,
    };
  }

  String get fullName {
    if (rank != null && rank!.isNotEmpty) {
      return '$rank $name';
    }
    return name;
  }
}
