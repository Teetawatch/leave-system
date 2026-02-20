import 'package:flutter/material.dart';
import 'package:table_calendar/table_calendar.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../config/app_theme.dart';
import '../models/leave_request_model.dart';
import '../models/guard_change_model.dart';
import '../models/holiday_model.dart';
import '../utils/thai_holidays.dart';

class LeaveCalendarView extends StatefulWidget {
  final List<LeaveRequest> leaveRequests;
  final List<GuardChangeRequest> guardRequests;

  const LeaveCalendarView({
    super.key,
    required this.leaveRequests,
    required this.guardRequests,
  });

  @override
  State<LeaveCalendarView> createState() => _LeaveCalendarViewState();
}

class _LeaveCalendarViewState extends State<LeaveCalendarView> {
  late Map<DateTime, List<dynamic>> _events;
  DateTime _focusedDay = DateTime.now();
  DateTime? _selectedDay;

  @override
  void initState() {
    super.initState();
    _selectedDay = _focusedDay;
    _events = _groupEvents(widget.leaveRequests, widget.guardRequests);
  }

  @override
  void didUpdateWidget(covariant LeaveCalendarView oldWidget) {
    super.didUpdateWidget(oldWidget);
    _events = _groupEvents(widget.leaveRequests, widget.guardRequests);
  }

  Map<DateTime, List<dynamic>> _groupEvents(
    List<LeaveRequest> leaves,
    List<GuardChangeRequest> guards,
  ) {
    final Map<DateTime, List<dynamic>> data = {};

    // Process Holidays
    for (var holiday in ThaiHolidays.all) {
      final date = DateTime(
        holiday.date.year,
        holiday.date.month,
        holiday.date.day,
      );
      if (data[date] == null) data[date] = [];
      data[date]!.add(holiday);
    }

    // Process Leaves
    for (var leave in leaves) {
      if (leave.status == 'rejected' || leave.status == 'cancelled') continue;

      DateTime current = leave.startDate;
      // Normalize to midnight
      current = DateTime(current.year, current.month, current.day);
      final end = DateTime(
        leave.endDate.year,
        leave.endDate.month,
        leave.endDate.day,
      );

      while (current.isBefore(end) || isSameDay(current, end)) {
        final date = DateTime(current.year, current.month, current.day);
        if (data[date] == null) data[date] = [];
        data[date]!.add(leave);
        current = current.add(const Duration(days: 1));
      }
    }

    // Process Guard Changes(if any)
    for (var guard in guards) {
      if (guard.status == 'rejected' || guard.status == 'cancelled') continue;

      try {
        final dateParsed = DateTime.parse(guard.dutyDate);
        final date = DateTime(
          dateParsed.year,
          dateParsed.month,
          dateParsed.day,
        );
        if (data[date] == null) data[date] = [];
        data[date]!.add(guard);
      } catch (e) {
        // print(e);
      }
    }

    return data;
  }

  List<dynamic> _getEventsForDay(DateTime day) {
    return _events[DateTime(day.year, day.month, day.day)] ?? [];
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Card(
          margin: const EdgeInsets.fromLTRB(16, 8, 16, 16),
          elevation: 4,
          shadowColor: Colors.black.withOpacity(0.1),
          color: Colors.white,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(24),
          ),
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 8.0, horizontal: 8.0),
            child: TableCalendar(
              locale: 'th_TH',
              firstDay: DateTime.now().subtract(const Duration(days: 365)),
              lastDay: DateTime.now().add(const Duration(days: 365)),
              focusedDay: _focusedDay,
              selectedDayPredicate: (day) => isSameDay(_selectedDay, day),
              calendarFormat: CalendarFormat.month,
              startingDayOfWeek: StartingDayOfWeek.sunday,
              availableGestures: AvailableGestures.horizontalSwipe,
              eventLoader: _getEventsForDay,
              onDaySelected: (selectedDay, focusedDay) {
                if (!isSameDay(_selectedDay, selectedDay)) {
                  setState(() {
                    _selectedDay = selectedDay;
                    _focusedDay = focusedDay;
                  });
                }
              },
              onPageChanged: (focusedDay) {
                _focusedDay = focusedDay;
              },
              calendarStyle: CalendarStyle(
                outsideDaysVisible: false,
                weekendTextStyle: GoogleFonts.kanit(
                  color: const Color(0xFFEF4444),
                ),
                defaultTextStyle: GoogleFonts.kanit(color: AppTheme.textMain),
                todayDecoration: BoxDecoration(
                  color: AppTheme.primary.withOpacity(0.3),
                  shape: BoxShape.circle,
                ),
                selectedDecoration: const BoxDecoration(
                  color: AppTheme.primary,
                  shape: BoxShape.circle,
                ),
                markerDecoration: const BoxDecoration(
                  color: AppTheme.secondary,
                  shape: BoxShape.circle,
                ),
              ),
              headerStyle: HeaderStyle(
                formatButtonVisible: false,
                titleCentered: true,
                titleTextStyle: GoogleFonts.kanit(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: AppTheme.textMain,
                ),
                leftChevronIcon: const Icon(
                  Icons.chevron_left_rounded,
                  color: AppTheme.textMain,
                ),
                rightChevronIcon: const Icon(
                  Icons.chevron_right_rounded,
                  color: AppTheme.textMain,
                ),
              ),
              daysOfWeekStyle: DaysOfWeekStyle(
                weekdayStyle: GoogleFonts.kanit(color: AppTheme.textSub),
                weekendStyle: GoogleFonts.kanit(color: const Color(0xFFEF4444)),
              ),
              calendarBuilders: CalendarBuilders(
                markerBuilder: (context, day, events) {
                  if (events.isEmpty) return null;
                  return Positioned(
                    bottom: 1,
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: events.take(4).map((event) {
                        return Container(
                          margin: const EdgeInsets.symmetric(horizontal: 1.0),
                          width: 5,
                          height: 5,
                          decoration: BoxDecoration(
                            color: _getEventColor(event),
                            shape: BoxShape.circle,
                          ),
                        );
                      }).toList(),
                    ),
                  );
                },
              ),
            ),
          ),
        ),

        // Event List Header
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
          child: Row(
            children: [
              Text(
                'รายการประจำวัน',
                style: GoogleFonts.kanit(
                  fontSize: 18,
                  fontWeight: FontWeight.w600,
                  color: AppTheme.textMain,
                ),
              ),
              const Spacer(),
              if (_selectedDay != null)
                Text(
                  DateFormat('d MMM yyyy', 'th_TH').format(_selectedDay!),
                  style: GoogleFonts.kanit(
                    fontSize: 14,
                    color: AppTheme.textSub,
                  ),
                ),
            ],
          ),
        ),

        // Event List
        Expanded(child: _buildEventList()),
      ],
    );
  }

  Color _getEventColor(dynamic event) {
    if (event is LeaveRequest) {
      if (event.status == 'approved') return const Color(0xFF10B981);
      if ([
        'pending',
        'waiting_head',
        'waiting_hr',
        'waiting_director',
      ].contains(event.status)) {
        return const Color(0xFFF59E0B);
      }
      return Colors.grey;
    } else if (event is GuardChangeRequest) {
      return const Color(0xFF3B82F6);
    } else if (event is Holiday) {
      return const Color(0xFFEF4444);
    }
    return Colors.grey;
  }

  Widget _buildEventList() {
    final events = _getEventsForDay(_selectedDay ?? DateTime.now());

    if (events.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.event_available_rounded,
              size: 48,
              color: Colors.grey.withOpacity(0.3),
            ),
            const SizedBox(height: 16),
            Text(
              'ไม่มีรายการในวันที่เลือก',
              style: GoogleFonts.kanit(color: Colors.grey),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.fromLTRB(20, 0, 20, 20),
      itemCount: events.length,
      itemBuilder: (context, index) {
        final event = events[index];
        if (event is LeaveRequest) {
          return _buildLeaveEventCard(event);
        } else if (event is GuardChangeRequest) {
          return _buildGuardEventCard(event);
        } else if (event is Holiday) {
          return _buildHolidayEventCard(event);
        }
        return const SizedBox();
      },
    );
  }

  Widget _buildLeaveEventCard(LeaveRequest request) {
    final color = _getEventColor(request);

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(16),
        child: IntrinsicHeight(
          child: Row(
            children: [
              Container(width: 6, color: color),
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Expanded(
                            child: Text(
                              request.leaveType.name,
                              style: GoogleFonts.kanit(
                                fontWeight: FontWeight.bold,
                                fontSize: 16,
                                color: AppTheme.textMain,
                              ),
                            ),
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 8,
                              vertical: 2,
                            ),
                            decoration: BoxDecoration(
                              color: color.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Text(
                              request.statusLabel,
                              style: GoogleFonts.kanit(
                                fontSize: 10,
                                fontWeight: FontWeight.bold,
                                color: color,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Text(
                        'เหตุผล: ${request.reason}',
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: GoogleFonts.kanit(
                          fontSize: 12,
                          color: AppTheme.textSub,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          Icon(
                            Icons.calendar_today_rounded,
                            size: 12,
                            color: AppTheme.textSub,
                          ),
                          const SizedBox(width: 4),
                          Text(
                            _formatDateRange(
                              request.startDate,
                              request.endDate,
                            ),
                            style: GoogleFonts.sarabun(
                              fontSize: 12,
                              color: AppTheme.textMain,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildGuardEventCard(GuardChangeRequest request) {
    const color = Color(0xFF3B82F6);

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(16),
        child: IntrinsicHeight(
          child: Row(
            children: [
              Container(width: 6, color: color),
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Expanded(
                            child: Text(
                              'เปลี่ยนเวร: ${request.dutyPositionThai}',
                              style: GoogleFonts.kanit(
                                fontWeight: FontWeight.bold,
                                fontSize: 16,
                                color: AppTheme.textMain,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 4),
                      if (request.remarks != null)
                        Text(
                          'หมายเหตุ: ${request.remarks}',
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: GoogleFonts.kanit(
                            fontSize: 12,
                            color: AppTheme.textSub,
                          ),
                        ),
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          Icon(
                            Icons.calendar_today_rounded,
                            size: 12,
                            color: AppTheme.textSub,
                          ),
                          const SizedBox(width: 4),
                          Text(
                            request.formattedDutyDate,
                            style: GoogleFonts.sarabun(
                              fontSize: 12,
                              color: AppTheme.textMain,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHolidayEventCard(Holiday holiday) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(16),
        child: IntrinsicHeight(
          child: Row(
            children: [
              Container(width: 6, color: const Color(0xFFEF4444)),
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: const Color(0xFFEF4444).withOpacity(0.1),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(
                          Icons.celebration_rounded,
                          color: Color(0xFFEF4444),
                          size: 20,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          holiday.name,
                          style: GoogleFonts.kanit(
                            fontSize: 16,
                            fontWeight: FontWeight.w600,
                            color: AppTheme.textMain,
                          ),
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 2,
                        ),
                        decoration: BoxDecoration(
                          color: const Color(0xFFEF4444).withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          'วันหยุด',
                          style: GoogleFonts.kanit(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFFEF4444),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  String _formatDateRange(DateTime start, DateTime end) {
    // Simple formatter, you can use existing global one if preferred
    final dateFormat = DateFormat('d MMM yy', 'th_TH');
    if (isSameDay(start, end)) {
      return dateFormat.format(start);
    }
    return '${dateFormat.format(start)} - ${dateFormat.format(end)}';
  }
}
