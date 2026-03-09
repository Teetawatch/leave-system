class DutyRosterUser {
  final int id;
  final String name;
  final String rank;

  DutyRosterUser({required this.id, required this.name, required this.rank});

  factory DutyRosterUser.fromJson(Map<String, dynamic> json) {
    return DutyRosterUser(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      rank: json['rank'] ?? '',
    );
  }

  String get displayName => rank.isNotEmpty ? '$rank $name' : name;
}

class DutyRosterDay {
  final String date;
  final DutyRosterUser? seniorDutyOfficer;
  final DutyRosterUser? dutyOfficer;
  final DutyRosterUser? assistantDutyOfficer;
  final String? notes;

  DutyRosterDay({
    required this.date,
    this.seniorDutyOfficer,
    this.dutyOfficer,
    this.assistantDutyOfficer,
    this.notes,
  });

  factory DutyRosterDay.fromJson(Map<String, dynamic> json) {
    return DutyRosterDay(
      date: json['date'] ?? '',
      seniorDutyOfficer: json['senior_duty_officer'] != null
          ? DutyRosterUser.fromJson(json['senior_duty_officer'])
          : null,
      dutyOfficer: json['duty_officer'] != null
          ? DutyRosterUser.fromJson(json['duty_officer'])
          : null,
      assistantDutyOfficer: json['assistant_duty_officer'] != null
          ? DutyRosterUser.fromJson(json['assistant_duty_officer'])
          : null,
      notes: json['notes'],
    );
  }
}

class TodayGuardEntry {
  final String positionLabel;
  final String positionKey;
  final String guardName;
  final String guardRank;
  final String? avatarUrl;

  TodayGuardEntry({
    required this.positionLabel,
    required this.positionKey,
    required this.guardName,
    required this.guardRank,
    this.avatarUrl,
  });

  factory TodayGuardEntry.fromJson(Map<String, dynamic> json) {
    return TodayGuardEntry(
      positionLabel: json['position_label'] ?? '',
      positionKey: json['position_key'] ?? '',
      guardName: json['guard_name'] ?? '',
      guardRank: json['guard_rank'] ?? '',
      avatarUrl: json['avatar_url'],
    );
  }

  String get displayName =>
      guardRank.isNotEmpty ? '$guardRank $guardName' : guardName;
}
