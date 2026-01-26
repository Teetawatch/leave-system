class EmployeeContact {
  final String id;
  final String name;
  final String position;
  final String department;
  final String phoneNumber;
  final String? lineId;
  final String? avatarUrl;

  EmployeeContact({
    required this.id,
    required this.name,
    required this.position,
    required this.department,
    required this.phoneNumber,
    this.lineId,
    this.avatarUrl,
  });
}
