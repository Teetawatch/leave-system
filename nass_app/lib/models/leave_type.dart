class LeaveType {
  final int id;
  final String name;
  final String slug;
  final String? description;
  final int? maxDaysPerYear;
  final bool requiresAdvanceNotice;
  final int? advanceNoticeDays;
  final bool allowsRetroactive;

  LeaveType({
    required this.id,
    required this.name,
    required this.slug,
    this.description,
    this.maxDaysPerYear,
    this.requiresAdvanceNotice = false,
    this.advanceNoticeDays,
    this.allowsRetroactive = false,
  });

  factory LeaveType.fromJson(Map<String, dynamic> json) {
    return LeaveType(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
      description: json['description'],
      maxDaysPerYear: json['max_days_per_year'],
      requiresAdvanceNotice: json['requires_advance_notice'] ?? false,
      advanceNoticeDays: json['advance_notice_days'],
      allowsRetroactive: json['allows_retroactive'] ?? false,
    );
  }
}
