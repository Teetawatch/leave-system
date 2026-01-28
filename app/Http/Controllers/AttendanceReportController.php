<?php

namespace App\Http\Controllers;

use App\Models\FaceAttendance\FaStudent;
use App\Models\FaceAttendance\FaStudentAttendanceLog;
use App\Models\FaceAttendance\FaCourse;
use App\Models\FaceAttendance\FaEmployee;
use App\Models\FaceAttendance\FaAttendanceLog;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    /**
     * Display attendance report page (Student Reports)
     * Fetches data directly from face_attendance database
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $courseId = $request->input('course_id');
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Get courses for filter dropdown
        $courses = FaCourse::orderBy('created_at', 'desc')->get();

        // Group student logs by student and date for a more comprehensive report view
        $studentLogsQuery = FaStudentAttendanceLog::whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($courseId) {
            $studentLogsQuery->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $logs = $studentLogsQuery->select('student_id', \Illuminate\Support\Facades\DB::raw('DATE(scan_time) as scan_date'))
            ->groupBy('student_id', 'scan_date')
            ->orderBy('scan_date', 'desc')
            ->paginate(20);

        // Map grouped items to include morning and afternoon scan details
        $logs->getCollection()->transform(function ($item) {
            $dayLogs = FaStudentAttendanceLog::with(['student.course'])
                ->where('student_id', $item->student_id)
                ->whereDate('scan_time', $item->scan_date)
                ->get();

            // Support both period and scan_type (for compatibility)
            $morningLog = $dayLogs->where('period', 'morning')->sortBy('scan_time')->first()
                ?? $dayLogs->where('scan_type', 'in')->where('scan_time', '<', $item->scan_date . ' 12:00:00')->sortBy('scan_time')->first();

            $afternoonLog = $dayLogs->where('period', 'afternoon')->sortByDesc('scan_time')->first()
                ?? $dayLogs->where('scan_type', 'in')->where('scan_time', '>=', $item->scan_date . ' 12:00:00')->sortByDesc('scan_time')->first();

            // Hardcode Late Logic for UI: Morning before 08:30 is NOT late
            if ($morningLog) {
                // Carbon object scan_time can be used directly for comparison
                $scanTimeStr = $morningLog->scan_time->format('H:i:s');
                $morningLog->is_late = ($scanTimeStr > '08:30:00');
            }

            $item->morning = $morningLog;
            $item->afternoon = $afternoonLog;

            // Fallback for student data
            $item->student = $dayLogs->first()?->student ?? FaStudent::with('course')->find($item->student_id);
            return $item;
        });

        // Get summary statistics
        $totalScans = FaStudentAttendanceLog::whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        if ($courseId) {
            $totalScans->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }
        $totalScansCount = $totalScans->count();

        // Unique students scanned
        $uniqueStudents = FaStudentAttendanceLog::whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        if ($courseId) {
            $uniqueStudents->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }
        $uniqueStudentsCount = $uniqueStudents->distinct('student_id')->count('student_id');

        // Total students in course
        $totalStudentsQuery = FaStudent::where('is_active', true);
        if ($courseId) {
            $totalStudentsQuery->where('course_id', $courseId);
        }
        $totalStudents = $totalStudentsQuery->count();

        // Get all students in the selected course(s)
        $allStudentsQuery = FaStudent::where('is_active', true);
        if ($courseId) {
            $allStudentsQuery->where('course_id', $courseId);
        }
        $allStudents = $allStudentsQuery->with('course')->get();

        // Identify students who scanned on this date (check-in)
        $scannedStudentIds = FaStudentAttendanceLog::whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('scan_type', 'in')
            ->pluck('student_id')
            ->unique();

        // Absent students (haven't checked in at all)
        $absentStudents = $allStudents->filter(function ($student) use ($scannedStudentIds) {
            return !$scannedStudentIds->contains($student->id);
        });

        // Late students - recalculate based on new 08:30 rule for the UI statistics
        $lateStudentsQuery = FaStudentAttendanceLog::with('student.course')
            ->whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('scan_type', 'in')
            ->whereRaw("TIME(scan_time) > '08:30:00'")
            ->whereRaw("TIME(scan_time) < '12:00:00'");

        if ($courseId) {
            $lateStudentsQuery->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }
        $lateStudents = $lateStudentsQuery->orderBy('scan_time', 'desc')->get();

        // Count statistics
        $absentCount = $absentStudents->count();
        $lateCount = $lateStudents->unique('student_id')->count();

        // ===== ข้อมูลข้าราชการ (Employees) =====

        // Get employee attendance logs
        $employeeLogsQuery = FaAttendanceLog::with(['employee'])
            ->whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $employeeLogs = $employeeLogsQuery->orderBy('scan_time', 'desc')->paginate(20, ['*'], 'emp_page');

        // Total employee scans
        $totalEmployeeScans = FaAttendanceLog::whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();

        // Unique employees scanned
        $uniqueEmployeesCount = FaAttendanceLog::whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->distinct('employee_id')
            ->count('employee_id');

        // Total active employees
        $totalEmployees = FaEmployee::where('is_active', true)->count();

        // Get all active employees
        $allEmployees = FaEmployee::where('is_active', true)->get();

        // Get employee IDs who have scanning (check-in) in the date range
        $scannedEmployeeIds = FaAttendanceLog::whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('scan_type', 'in')
            ->pluck('employee_id')
            ->unique();

        // Get ALL approved leave requests for the date range
        $leaveRequests = LeaveRequest::with(['leaveType', 'user'])
            ->where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                // Check for date overlap:
                // (start_date <= end_range) AND (end_date >= start_range)
                $query->whereDate('start_date', '<=', $endDate)
                    ->whereDate('end_date', '>=', $startDate);
            })
            ->get();

        // Users on leave (for name matching)
        $usersOnLeave = collect();
        if ($leaveRequests->isNotEmpty()) {
            $userIdsOnLeave = $leaveRequests->pluck('user_id')->unique();
            $usersOnLeave = \App\Models\User::whereIn('id', $userIdsOnLeave)->get();
        }

        // Identify Absent Employees (Not scanned)
        $absentEmployeesRaw = $allEmployees->filter(function ($employee) use ($scannedEmployeeIds) {
            return !$scannedEmployeeIds->contains($employee->id);
        });

        // Partition Absent Employees into 'On Leave' vs 'True Absent'
        $onLeaveEmployees = collect();
        $trueAbsentEmployees = collect();

        foreach ($absentEmployeesRaw as $emp) {
            $matchedRequest = null;
            $empFullName = trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? ''));

            // 1. Try Match by User ID
            if ($emp->user_id) {
                $matchedRequest = $leaveRequests->first(function ($req) use ($emp) {
                    return $req->user_id == $emp->user_id;
                });
            }

            // 2. Try Match by Name (if no match yet)
            if (!$matchedRequest && $usersOnLeave->isNotEmpty()) {
                $matchedUser = $usersOnLeave->first(function ($user) use ($emp, $empFullName) {
                    if ($emp->user_id && $emp->user_id == $user->id)
                        return true;

                    // Fallback: Check if Employee Name (Longer, with title) contains User Name (Shorter, no title)
                    $userParts = array_filter(explode(' ', $user->name));
                    if (empty($userParts))
                        return false;

                    foreach ($userParts as $part) {
                        if (!str_contains($empFullName, trim($part))) {
                            return false;
                        }
                    }
                    return true;
                });

                if ($matchedUser) {
                    if (!$emp->user_id)
                        $emp->user_id = $matchedUser->id; // Fix missing ID
                    $matchedRequest = $leaveRequests->firstWhere('user_id', $matchedUser->id);
                }
            }

            if ($matchedRequest) {
                $emp->leave_info = $matchedRequest;
                $emp->leave_type_name = $matchedRequest->leaveType->name ?? 'ลางาน';
                $onLeaveEmployees->push($emp);
            } else {
                $trueAbsentEmployees->push($emp);
            }
        }

        // Late employees stats
        $lateEmployeeCount = FaAttendanceLog::whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('scan_type', 'in')
            ->where('is_late', true)
            ->distinct('employee_id')
            ->count('employee_id');

        // Late employees list
        $lateEmployeesQuery = FaAttendanceLog::with('employee')
            ->whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('scan_type', 'in')
            ->where('is_late', true);
        $lateEmployees = $lateEmployeesQuery->orderBy('scan_time', 'desc')->get();

        // Update final counts for view
        $lateEmployeeCount = $lateEmployees->unique('employee_id')->count();

        // Final collections
        $absentEmployees = $trueAbsentEmployees;
        $absentEmployeeCount = $trueAbsentEmployees->count();
        $onLeaveCount = $onLeaveEmployees->count();

        return view('attendance-reports.index', compact(
            'logs',
            'courses',
            'courseId',
            'startDate',
            'endDate',
            'totalScansCount',
            'uniqueStudentsCount',
            'totalStudents',
            'absentStudents',
            'lateStudents',
            'absentCount',
            'lateCount',
            // Employee data
            'employeeLogs',
            'totalEmployeeScans',
            'uniqueEmployeesCount',
            'totalEmployees',
            'absentEmployees',
            'lateEmployees',
            'absentEmployeeCount',
            'lateEmployeeCount',
            'lateEmployeeCount',
            'onLeaveCount',
            'onLeaveEmployees'
        ));
    }

    /**
     * Export attendance report to PDF
     * 
     * Two check-in periods:
     * - Morning: 05:30 - 08:00 (late after 08:00)
     * - Afternoon: 12:30 - 13:00 (late after 13:00)
     */
    public function exportPdf(Request $request)
    {
        // Get filter parameters
        $courseId = $request->input('course_id');
        $date = $request->input('date', now()->format('Y-m-d'));

        // Get all students in the selected course(s)
        $allStudentsQuery = FaStudent::where('is_active', true);
        if ($courseId) {
            $allStudentsQuery->where('course_id', $courseId);
        }
        $allStudents = $allStudentsQuery->with('course')->get();

        // Build query - get all logs for the selected date
        $query = FaStudentAttendanceLog::with(['student.course'])
            ->whereDate('scan_time', $date);

        if ($courseId) {
            $query->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $logs = $query->orderBy('student_id')->orderBy('scan_time')->get();

        // Get student IDs who have scanned on this date
        $scannedStudentIds = $logs->pluck('student_id')->unique();

        // Group scanned students by student_id with morning/afternoon periods
        $scannedStudentLogs = $logs->groupBy('student_id')->map(function ($studentLogs) {
            $morning = $studentLogs->where('period', 'morning')->sortBy('scan_time')->first()
                ?? $studentLogs->where('scan_time', '<', $studentLogs->first()->scan_time->format('Y-m-d') . ' 12:00:00')->sortBy('scan_time')->first();

            $afternoon = $studentLogs->where('period', 'afternoon')->sortByDesc('scan_time')->first()
                ?? $studentLogs->where('scan_time', '>=', $studentLogs->first()->scan_time->format('Y-m-d') . ' 12:00:00')->sortByDesc('scan_time')->first();

            // Hardcode Late Logic for PDF: Morning before 08:30 is NOT late
            $morningLate = false;
            if ($morning) {
                $scanTimeStr = $morning->scan_time->format('H:i:s');
                $morningLate = ($scanTimeStr > '08:30:00');
            }

            // Determine status based on scans
            $status = 'ปกติ';
            $afternoonLate = $afternoon && $afternoon->is_late;

            if ($morningLate || $afternoonLate) {
                $status = 'มาสาย';
            }
            if (!$morning && !$afternoon) {
                $status = 'ไม่มาลงชื่อ';
            }

            return [
                'student' => $studentLogs->first()->student,
                'morning' => $morning,
                'afternoon' => $afternoon,
                'morning_late' => $morningLate,
                'afternoon_late' => $afternoonLate,
                'status' => $status,
            ];
        })->values();

        // Add absent students (those who haven't scanned at all)
        $absentStudentLogs = $allStudents->filter(function ($student) use ($scannedStudentIds) {
            return !$scannedStudentIds->contains($student->id);
        })->map(function ($student) {
            return [
                'student' => $student,
                'morning' => null,
                'afternoon' => null,
                'morning_late' => false,
                'afternoon_late' => false,
                'status' => 'ไม่มาลงชื่อ',
            ];
        })->values();

        // Merge scanned students and absent students
        $studentLogs = $scannedStudentLogs->concat($absentStudentLogs);

        // Calculate totals
        $totalStudents = $allStudents->count();
        $presentCount = $studentLogs->where('status', 'ปกติ')->count();
        $lateCount = $studentLogs->where('status', 'มาสาย')->count();
        $absentCount = $studentLogs->where('status', 'ไม่มาลงชื่อ')->count();

        $courseName = $courseId ? FaCourse::find($courseId)?->name : 'ทุกหลักสูตร';
        $courses = FaCourse::orderBy('created_at', 'desc')->get();

        // ===== ข้อมูลข้าราชการ (Employees) =====

        // Get all active employees
        $allEmployees = FaEmployee::where('is_active', true)->get();

        // Build query - get all employee logs for the selected date
        $employeeQuery = FaAttendanceLog::with(['employee'])
            ->whereDate('scan_time', $date);

        $employeeLogRecords = $employeeQuery->orderBy('employee_id')->orderBy('scan_time')->get();

        // Get employee IDs who have scanned on this date
        $scannedEmployeeIds = $employeeLogRecords->pluck('employee_id')->unique();

        // Get approved official duty requests for the selected date
        $officialDutyType = LeaveType::where('slug', 'official-duty')->first();
        $officialDutyRequests = collect();
        if ($officialDutyType) {
            $officialDutyRequests = LeaveRequest::where('leave_type_id', $officialDutyType->id)
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->get();
        }

        // Fetch Users for Name Matching
        $usersOnDuty = collect();
        if ($officialDutyRequests->isNotEmpty()) {
            $usersOnDuty = \App\Models\User::whereIn('id', $officialDutyRequests->pluck('user_id'))->get();
        }

        // Helper to check official duty (Robust ID/Name Match)
        $checkOfficialDuty = function ($employee) use ($officialDutyRequests, $usersOnDuty) {
            $empFullName = trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''));

            // Match User
            $matchedUser = $usersOnDuty->first(function ($user) use ($employee, $empFullName) {
                if ($employee->user_id && $employee->user_id == $user->id)
                    return true;

                // Fallback: Check if Employee Name (Longer, with title) contains User Name (Shorter, no title)
                $userParts = array_filter(explode(' ', $user->name));
                if (empty($userParts))
                    return false;

                foreach ($userParts as $part) {
                    if (!str_contains($empFullName, trim($part))) {
                        return false;
                    }
                }
                return true;
            });

            if ($matchedUser) {
                return $officialDutyRequests->firstWhere('user_id', $matchedUser->id);
            }
            return null;
        };

        // Group scanned employees by employee_id - ข้าราชการสแกนแค่ตอนเช้าครั้งเดียว
        $scannedEmployeeLogs = $employeeLogRecords->groupBy('employee_id')->map(function ($empLogs) use ($checkOfficialDuty) {
            // ใช้ log แรกของวัน (เรียงตาม scan_time แล้ว)
            $firstLog = $empLogs->first();
            $employee = $firstLog->employee;

            // Check Official Duty FIRST
            $onDuty = $checkOfficialDuty($employee);

            if ($onDuty) {
                $status = 'ไปราชการ';
            } else {
                $status = 'ปกติ';
                $isLate = $firstLog && $firstLog->is_late;

                if ($isLate) {
                    $status = 'มาสาย';
                }
                if (!$firstLog) {
                    $status = 'ไม่มาลงชื่อ';
                }
            }

            return [
                'employee' => $employee,
                'morning' => $firstLog, // ใช้ log แรกเป็น morning
                'afternoon' => null,
                'morning_late' => $firstLog ? $firstLog->is_late : false,
                'afternoon_late' => false,
                'status' => $status,
            ];
        })->values();

        // Add absent employees (those who haven't scanned at all)
        $absentEmployeeLogs = $allEmployees->filter(function ($employee) use ($scannedEmployeeIds) {
            return !$scannedEmployeeIds->contains($employee->id);
        })->map(function ($employee) use ($checkOfficialDuty) {
            $onDuty = $checkOfficialDuty($employee);
            return [
                'employee' => $employee,
                'morning' => null,
                'afternoon' => null,
                'morning_late' => false,
                'afternoon_late' => false,
                'status' => $onDuty ? 'ไปราชการ' : 'ไม่มาลงชื่อ',
            ];
        })->values();

        // Merge scanned employees and absent employees
        $employeeLogs = $scannedEmployeeLogs->concat($absentEmployeeLogs);

        // Calculate employee totals
        $totalEmployees = $allEmployees->count();
        $employeePresentCount = $employeeLogs->where('status', 'ปกติ')->count();
        $employeeLateCount = $employeeLogs->where('status', 'มาสาย')->count();
        $employeeAbsentCount = $employeeLogs->where('status', 'ไม่มาลงชื่อ')->count();
        $employeeOfficialDutyCount = $employeeLogs->where('status', 'ไปราชการ')->count();

        return view('attendance-reports.pdf', compact(
            'studentLogs',
            'courseName',
            'date',
            'courses',
            'courseId',
            'totalStudents',
            'presentCount',
            'lateCount',
            'absentCount',
            // Employee data
            'employeeLogs',
            'totalEmployees',
            'employeePresentCount',
            'employeeLateCount',
            'employeeAbsentCount',
            'employeeOfficialDutyCount'
        ));
    }

    /**
     * Get attendance summary for dashboard widget (AJAX)
     */
    public function dashboardSummary()
    {
        $today = now()->format('Y-m-d');

        // Total students
        $totalStudents = FaStudent::where('is_active', true)->count();

        // Students who scanned today
        $scannedToday = FaStudentAttendanceLog::whereDate('scan_time', $today)
            ->distinct('student_id')
            ->count('student_id');

        // Late students today
        $lateToday = FaStudentAttendanceLog::whereDate('scan_time', $today)
            ->where('is_late', true)
            ->distinct('student_id')
            ->count('student_id');

        return response()->json([
            'total_students' => $totalStudents,
            'scanned_today' => $scannedToday,
            'late_today' => $lateToday,
            'absent_today' => $totalStudents - $scannedToday,
        ]);
    }
}
