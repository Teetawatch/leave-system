class LeaveType {
  final int id;
  final String name;
  final String slug;
  final int maxDays;
  final bool requiresAdvanceNotice;
  final bool allowsRetroactive;

  LeaveType({
    required this.id,
    required this.name,
    required this.slug,
    required this.maxDays,
    required this.requiresAdvanceNotice,
    required this.allowsRetroactive,
  });

  factory LeaveType.fromJson(Map<String, dynamic> json) {
    return LeaveType(
      id: json['id'],
      name: json['name'],
      slug: json['slug'] ?? '',
      maxDays: json['max_days_per_year'] ?? 0, // Adjust based on API response
      requiresAdvanceNotice:
          json['requires_advance_notice'] == 1 ||
          json['requires_advance_notice'] == true,
      allowsRetroactive:
          json['allows_retroactive'] == 1 || json['allows_retroactive'] == true,
    );
  }
}
