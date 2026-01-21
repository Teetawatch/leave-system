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

        // Build query
        $query = FaStudentAttendanceLog::with(['student.course'])
            ->whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($courseId) {
            $query->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $logs = $query->orderBy('scan_time', 'desc')->paginate(20);

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

        // Get student IDs who have scanned (check-in) in the date range
        $scannedStudentIds = FaStudentAttendanceLog::whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('scan_type', 'in');
        if ($courseId) {
            $scannedStudentIds->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }
        $scannedStudentIds = $scannedStudentIds->pluck('student_id')->unique();

        // Absent students (haven't checked in at all)
        $absentStudents = $allStudents->filter(function ($student) use ($scannedStudentIds) {
            return !$scannedStudentIds->contains($student->id);
        });

        // Late students (scanned with is_late = true)
        $lateStudentsQuery = FaStudentAttendanceLog::with('student.course')
            ->whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('scan_type', 'in')
            ->where('is_late', true);
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

        // Get approved official duty requests for the date range
        $officialDutyType = LeaveType::where('slug', 'official-duty')->first();
        $officialDutyRequests = collect();
        if ($officialDutyType) {
            $officialDutyRequests = LeaveRequest::where('leave_type_id', $officialDutyType->id)
                ->where('status', 'approved')
                ->where(function ($query) use ($startDate, $endDate) {
                    // Check for date overlap:
                    // (start_date <= end_range) AND (end_date >= start_range)
                    $query->whereDate('start_date', '<=', $endDate)
                        ->whereDate('end_date', '>=', $startDate);
                })
                ->get();
        }

        // Absent employees (haven't checked in at all)
        $absentEmployees = $allEmployees->filter(function ($employee) use ($scannedEmployeeIds) {
            return !$scannedEmployeeIds->contains($employee->id);
        })->map(function ($employee) use ($officialDutyRequests) {
            // Check if this employee has an official duty duty request
            // We assume employee->user_id links to our User model
            $duty = $officialDutyRequests->firstWhere('user_id', $employee->user_id);
            $employee->on_official_duty = $duty ? true : false;
            $employee->official_duty_reason = $duty ? $duty->reason : null;
            return $employee;
        });

        // Match official duty requests to FaEmployees
        $onOfficialDutyEmployees = collect();
        if ($officialDutyRequests->isNotEmpty()) {
            $userIdsOnDuty = $officialDutyRequests->pluck('user_id')->unique();
            $onOfficialDutyEmployees = $allEmployees->whereIn('user_id', $userIdsOnDuty)->map(function ($emp) use ($officialDutyRequests) {
                $duty = $officialDutyRequests->firstWhere('user_id', $emp->user_id);
                $emp->official_duty_reason = $duty ? $duty->reason : null;
                $emp->on_official_duty = true;
                return $emp;
            });
        }

        // Split absent into actually absent and on official duty
        // Actually absent = Absent AND Not on official duty
        $actuallyAbsentEmployees = $absentEmployees->where('on_official_duty', false);

        // Update counts
        $absentEmployeeCount = $actuallyAbsentEmployees->count();
        $officialDutyCount = $onOfficialDutyEmployees->count();
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

        // Count employee statistics
        $absentEmployeeCount = $absentEmployees->count();
        $lateEmployeeCount = $lateEmployees->unique('employee_id')->count();

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
            'officialDutyCount',
            'onOfficialDutyEmployees'
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
            $morning = $studentLogs->where('period', 'morning')->first();
            $afternoon = $studentLogs->where('period', 'afternoon')->first();

            // Determine status based on scans
            $status = 'ปกติ';
            $morningLate = $morning && $morning->is_late;
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

        // Helper to check official duty
        $checkOfficialDuty = function ($userId) use ($officialDutyRequests) {
            return $officialDutyRequests->firstWhere('user_id', $userId);
        };

        // Group scanned employees by employee_id - ข้าราชการสแกนแค่ตอนเช้าครั้งเดียว
        $scannedEmployeeLogs = $employeeLogRecords->groupBy('employee_id')->map(function ($empLogs) use ($checkOfficialDuty) {
            // ใช้ log แรกของวัน (เรียงตาม scan_time แล้ว)
            $firstLog = $empLogs->first();
            $employee = $firstLog->employee;

            // Check Official Duty FIRST
            $onDuty = $checkOfficialDuty($employee->user_id);

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
            $onDuty = $checkOfficialDuty($employee->user_id);
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
