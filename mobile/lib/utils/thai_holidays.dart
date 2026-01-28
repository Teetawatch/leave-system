import '../models/holiday_model.dart';

class ThaiHolidays {
  static List<Holiday> get all {
    return [
      // 2026
      Holiday(date: DateTime(2026, 1, 1), name: "วันขึ้นปีใหม่"),
      Holiday(date: DateTime(2026, 3, 3), name: "วันมาฆบูชา"), // Estimated
      Holiday(date: DateTime(2026, 4, 6), name: "วันจักรี"),
      Holiday(date: DateTime(2026, 4, 13), name: "วันสงกรานต์"),
      Holiday(date: DateTime(2026, 4, 14), name: "วันสงกรานต์"),
      Holiday(date: DateTime(2026, 4, 15), name: "วันสงกรานต์"),
      Holiday(date: DateTime(2026, 5, 1), name: "วันแรงงานแห่งชาติ"),
      Holiday(date: DateTime(2026, 5, 4), name: "วันฉัตรมงคล"),
      Holiday(date: DateTime(2026, 5, 31), name: "วันวิสาขบูชา"), // Estimated
      Holiday(
        date: DateTime(2026, 6, 3),
        name: "วันเฉลิมพระชนมพรรษา พระราชินี",
      ),
      Holiday(date: DateTime(2026, 7, 28), name: "วันเฉลิมพระชนมพรรษา ร.10"),
      Holiday(date: DateTime(2026, 7, 29), name: "วันอาสาฬหบูชา"), // Estimated
      Holiday(date: DateTime(2026, 7, 30), name: "วันเข้าพรรษา"), // Estimated
      Holiday(date: DateTime(2026, 8, 12), name: "วันแม่แห่งชาติ"),
      Holiday(date: DateTime(2026, 10, 13), name: "วันนวมินทรมหาราช"),
      Holiday(date: DateTime(2026, 10, 23), name: "วันปิยมหาราช"),
      Holiday(date: DateTime(2026, 12, 5), name: "วันพ่อแห่งชาติ"),
      Holiday(date: DateTime(2026, 12, 10), name: "วันรัฐธรรมนูญ"),
      Holiday(date: DateTime(2026, 12, 31), name: "วันสิ้นปี"),

      // 2025 (In case user scrolls back)
      Holiday(date: DateTime(2025, 1, 1), name: "วันขึ้นปีใหม่"),
      Holiday(date: DateTime(2025, 2, 12), name: "วันมาฆบูชา"),
      Holiday(date: DateTime(2025, 4, 6), name: "วันจักรี"),
      Holiday(date: DateTime(2025, 4, 13), name: "วันสงกรานต์"),
      Holiday(date: DateTime(2025, 4, 14), name: "วันสงกรานต์"),
      Holiday(date: DateTime(2025, 4, 15), name: "วันสงกรานต์"),
      Holiday(date: DateTime(2025, 5, 1), name: "วันแรงงานแห่งชาติ"),
      Holiday(date: DateTime(2025, 5, 4), name: "วันฉัตรมงคล"),
      Holiday(date: DateTime(2025, 5, 11), name: "วันวิสาขบูชา"),
      Holiday(
        date: DateTime(2025, 6, 3),
        name: "วันเฉลิมพระชนมพรรษา พระราชินี",
      ),
      Holiday(date: DateTime(2025, 7, 10), name: "วันอาสาฬหบูชา"),
      Holiday(date: DateTime(2025, 7, 11), name: "วันเข้าพรรษา"),
      Holiday(date: DateTime(2025, 7, 28), name: "วันเฉลิมพระชนมพรรษา ร.10"),
      Holiday(date: DateTime(2025, 8, 12), name: "วันแม่แห่งชาติ"),
      Holiday(date: DateTime(2025, 10, 13), name: "วันคล้ายวันสวรรคต ร.9"),
      Holiday(date: DateTime(2025, 10, 23), name: "วันปิยมหาราช"),
      Holiday(date: DateTime(2025, 12, 5), name: "วันพ่อแห่งชาติ"),
      Holiday(date: DateTime(2025, 12, 10), name: "วันรัฐธรรมนูญ"),
      Holiday(date: DateTime(2025, 12, 31), name: "วันสิ้นปี"),
    ];
  }
}
