<?php

namespace App\Http\Controllers;

use App\Models\FaceAttendance\FaStudent;
use App\Models\FaceAttendance\FaStudentAttendanceLog;
use App\Models\FaceAttendance\FaCourse;
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
            'lateCount'
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

        return view('attendance-reports.pdf', compact(
            'studentLogs', 
            'courseName', 
            'date', 
            'courses', 
            'courseId',
            'totalStudents',
            'presentCount',
            'lateCount',
            'absentCount'
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
