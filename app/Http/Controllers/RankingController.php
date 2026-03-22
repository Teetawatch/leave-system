<?php

namespace App\Http\Controllers;

use App\Models\FaceAttendance\FaAttendanceLog;
use App\Models\FaceAttendance\FaEmployee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month');

        // Allowed Departments
        $targetDepartments = ['แผนกปกครอง', 'แผนกศึกษา', 'แผนกสนับสนุน', 'ฝ่ายธุรการ', 'ฝ่ายการเงิน'];

        // 1. Attendance Ranks (from face_attendance database)
        $attendanceQuery = FaAttendanceLog::query()
            ->whereHas('employee', function ($q) use ($targetDepartments) {
                $q->whereIn('department', $targetDepartments);
            })
            ->where('scan_type', 'in')
            ->whereYear('scan_time', $year);

        if ($month) {
            $attendanceQuery->whereMonth('scan_time', $month);
        }

        // นักมาทำงานดีเด่น / นาฬิกาชีวิตแม่นยำ (Most Scans)
        $mostScans = (clone $attendanceQuery)
            ->select('employee_id', DB::raw('count(*) as count'))
            ->groupBy('employee_id')
            ->orderByDesc('count')
            ->with('employee')
            ->take(5)
            ->get();

        // มาเช้าดีเด่น (Earliest Average Scan Time)
        $earlyBirds = (clone $attendanceQuery)
            ->select('employee_id', DB::raw('AVG(TIME_TO_SEC(TIME(scan_time))) as avg_sec'))
            ->groupBy('employee_id')
            ->orderBy('avg_sec')
            ->with('employee')
            ->take(5)
            ->get();

        // ไม่เคยสายดีเด่น (Most Scans with 0 Late)
        $neverLate = (clone $attendanceQuery)
            ->select('employee_id', DB::raw('count(*) as count'))
            ->where('is_late', false)
            ->groupBy('employee_id')
            ->having(DB::raw('SUM(CASE WHEN is_late = 1 THEN 1 ELSE 0 END)'), '=', 0)
            ->orderByDesc('count')
            ->with('employee')
            ->take(5)
            ->get();

        // มาสายที่สุด (Most Late)
        $mostLate = (clone $attendanceQuery)
            ->select('employee_id', DB::raw('count(*) as late_count'))
            ->where('is_late', true)
            ->groupBy('employee_id')
            ->orderByDesc('late_count')
            ->with('employee')
            ->take(5)
            ->get();

        // 2. Leave Ranks (from main database)
        // Exclude Official Duty (slug: official-duty)
        $officialDutyId = LeaveType::where('slug', 'official-duty')->value('id');

        $leaveQuery = LeaveRequest::query()
            ->whereHas('user', function ($q) use ($targetDepartments) {
                $q->whereIn('department', $targetDepartments);
            })
            ->where('status', 'approved')
            ->whereYear('start_date', $year);

        if ($officialDutyId) {
            $leaveQuery->where('leave_type_id', '!=', $officialDutyId);
        }

        if ($month) {
            $leaveQuery->whereMonth('start_date', $month);
        }

        // ราชาแห่งการลา (Most Total Days)
        $kingOfLeave = (clone $leaveQuery)
            ->select('user_id', DB::raw('SUM(total_days) as total_days'))
            ->groupBy('user_id')
            ->orderByDesc('total_days')
            ->with('user')
            ->take(5)
            ->get();

        // นักลาดีเด่น / ขอลาได้ทุกสถานการณ์ (Most Requests)
        $mostRequests = (clone $leaveQuery)
            ->select('user_id', DB::raw('count(*) as count'))
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->with('user')
            ->take(5)
            ->get();

        // ลาทีไร หายยาว (Highest Average Days)
        $longAbsence = (clone $leaveQuery)
            ->select('user_id', DB::raw('AVG(total_days) as avg_days'))
            ->groupBy('user_id')
            ->having(DB::raw('count(*)'), '>', 0)
            ->orderByDesc('avg_days')
            ->with('user')
            ->take(5)
            ->get();

        // ขอลาได้ทุกสถานการณ์ (Most Diverse Leave Types)
        $diverseLeave = (clone $leaveQuery)
            ->select('user_id', DB::raw('COUNT(DISTINCT leave_type_id) as types_count'))
            ->groupBy('user_id')
            ->orderByDesc('types_count')
            ->with('user')
            ->take(5)
            ->get();

        return Inertia::render('Ranking/Index', compact(
            'mostScans',
            'earlyBirds',
            'neverLate',
            'mostLate',
            'kingOfLeave',
            'mostRequests',
            'longAbsence',
            'diverseLeave',
            'year',
            'month'
        ));
    }
}
