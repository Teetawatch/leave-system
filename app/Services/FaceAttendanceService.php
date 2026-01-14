<?php

namespace App\Services;

use App\Models\FaceAttendance\FaAttendanceLog;
use App\Models\FaceAttendance\FaStudentAttendanceLog;
use App\Models\FaceAttendance\FaEmployee;
use App\Models\FaceAttendance\FaStudent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FaceAttendanceService
{
    protected bool $useDatabase = true;

    /**
     * Get attendance report for a date range
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int|null $departmentId (not used in direct DB mode)
     * @param string $type - 'staff', 'student', or 'all'
     * @return array
     */
    public function getAttendanceReport(?string $startDate = null, ?string $endDate = null, ?int $departmentId = null, string $type = 'all'): array
    {
        try {
            $startDate = $startDate ?: now()->startOfMonth()->format('Y-m-d');
            $endDate = $endDate ?: now()->format('Y-m-d');

            $allData = [];

            // Fetch staff attendance if type is 'staff' or 'all'
            if ($type === 'staff' || $type === 'all') {
                $staffData = $this->getStaffAttendanceFromDb($startDate, $endDate);
                $allData = array_merge($allData, $staffData);
            }

            // Fetch student attendance if type is 'student' or 'all'
            if ($type === 'student' || $type === 'all') {
                $studentData = $this->getStudentAttendanceFromDb($startDate, $endDate);
                $allData = array_merge($allData, $studentData);
            }

            // Sort by date descending, then by name
            usort($allData, function ($a, $b) {
                $dateCompare = strcmp($b['date'] ?? '', $a['date'] ?? '');
                if ($dateCompare !== 0) return $dateCompare;
                return strcmp($a['employee_name'] ?? '', $b['employee_name'] ?? '');
            });

            return [
                'success' => true,
                'data' => $allData,
                'meta' => ['type' => $type, 'source' => 'database'],
            ];

        } catch (\Exception $e) {
            Log::error('Face Attendance Database Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Get staff attendance from database
     */
    protected function getStaffAttendanceFromDb(string $startDate, string $endDate): array
    {
        $logs = FaAttendanceLog::with('employee')
            ->whereDate('scan_time', '>=', $startDate)
            ->whereDate('scan_time', '<=', $endDate)
            ->orderBy('scan_time', 'desc')
            ->get();

        // Group by employee and date to pair check-in/check-out
        $grouped = $logs->groupBy(function ($log) {
            return $log->employee_id . '_' . $log->scan_time->format('Y-m-d');
        });

        $result = [];
        foreach ($grouped as $key => $dayLogs) {
            $firstLog = $dayLogs->first();
            $employee = $firstLog->employee;
            
            if (!$employee) continue;

            $checkIn = $dayLogs->where('scan_type', 'in')->sortBy('scan_time')->first();
            $checkOut = $dayLogs->where('scan_type', 'out')->sortByDesc('scan_time')->first();

            // Determine status
            $status = 'on_time';
            if ($checkIn && $checkIn->is_late) {
                $status = 'late';
            } elseif (!$checkIn) {
                $status = 'absent';
            }

            $result[] = [
                'date' => $firstLog->scan_time->format('Y-m-d'),
                'employee_code' => $employee->employee_code,
                'employee_name' => $employee->full_name,
                'department' => $employee->department,
                'position' => $employee->position,
                'check_in' => $checkIn ? $checkIn->scan_time->format('H:i') : null,
                'check_out' => $checkOut ? $checkOut->scan_time->format('H:i') : null,
                'status' => $status,
                'photo' => $this->getPhotoUrl($checkIn->snapshot_path ?? $employee->photo_path ?? null),
                'person_type' => 'staff',
                'note' => '',
            ];
        }

        return $result;
    }

    /**
     * Get student attendance from database
     */
    protected function getStudentAttendanceFromDb(string $startDate, string $endDate): array
    {
        $logs = FaStudentAttendanceLog::with(['student', 'student.course'])
            ->whereDate('scan_time', '>=', $startDate)
            ->whereDate('scan_time', '<=', $endDate)
            ->orderBy('scan_time', 'desc')
            ->get();

        // Group by student and date to pair morning/afternoon
        $grouped = $logs->groupBy(function ($log) {
            return $log->student_id . '_' . $log->scan_time->format('Y-m-d');
        });

        $result = [];
        foreach ($grouped as $key => $dayLogs) {
            $firstLog = $dayLogs->first();
            $student = $firstLog->student;
            
            if (!$student) continue;

            $morningLog = $dayLogs->where('period', 'morning')->sortBy('scan_time')->first();
            $afternoonLog = $dayLogs->where('period', 'afternoon')->sortByDesc('scan_time')->first();

            // If no period specified, use scan_type
            if (!$morningLog) {
                $morningLog = $dayLogs->where('scan_type', 'in')->sortBy('scan_time')->first();
            }
            if (!$afternoonLog) {
                $afternoonLog = $dayLogs->where('scan_type', 'out')->sortByDesc('scan_time')->first();
            }

            // Determine status
            $status = 'on_time';
            if ($morningLog && $morningLog->is_late) {
                $status = 'late';
            } elseif (!$morningLog) {
                $status = 'absent';
            }

            $courseName = $student->course ? $student->course->name : 'ไม่ระบุหลักสูตร';

            $result[] = [
                'date' => $firstLog->scan_time->format('Y-m-d'),
                'employee_code' => $student->student_code ?? null,
                'employee_name' => $student->full_name,
                'department' => $courseName,
                'position' => null,
                'check_in' => $morningLog ? $morningLog->scan_time->format('H:i') : null,
                'check_out' => $afternoonLog ? $afternoonLog->scan_time->format('H:i') : null,
                'status' => $status,
                'photo' => $this->getPhotoUrl($morningLog->snapshot_path ?? $student->photo_path ?? null),
                'person_type' => 'student',
                'note' => '',
            ];
        }

        return $result;
    }

    /**
     * Get today's attendance summary
     */
    public function getTodaySummary(): array
    {
        $today = now()->format('Y-m-d');
        $cacheKey = "attendance_summary_{$today}";
        
        return Cache::remember($cacheKey, 300, function () use ($today) {
            $result = $this->getAttendanceReport($today, $today);
            
            if (!$result['success']) {
                return [
                    'total' => 0,
                    'on_time' => 0,
                    'late' => 0,
                    'absent' => 0,
                    'error' => $result['error'] ?? null,
                ];
            }

            $data = $result['data'];
            
            return [
                'total' => count($data),
                'on_time' => collect($data)->where('status', 'on_time')->count(),
                'late' => collect($data)->where('status', 'late')->count(),
                'absent' => collect($data)->where('status', 'absent')->count(),
                'staff_count' => collect($data)->where('person_type', 'staff')->count(),
                'student_count' => collect($data)->where('person_type', 'student')->count(),
            ];
        });
    }

    /**
     * Get attendance for a specific employee
     */
    public function getEmployeeAttendance(string $employeeCode, ?string $startDate = null, ?string $endDate = null): array
    {
        $result = $this->getAttendanceReport($startDate, $endDate, null, 'staff');
        
        if (!$result['success']) {
            return $result;
        }

        $filtered = collect($result['data'])
            ->where('employee_code', $employeeCode)
            ->values()
            ->all();

        return [
            'success' => true,
            'data' => $filtered,
        ];
    }

    /**
     * Clear attendance cache
     */
    public function clearCache(): void
    {
        $today = now()->format('Y-m-d');
        Cache::forget("attendance_summary_{$today}");
    }

    /**
     * Check database connection
     */
    public function checkConnection(): bool
    {
        try {
            DB::connection('face_attendance')->getPdo();
            return true;
        } catch (\Exception $e) {
            Log::error('Face Attendance DB Connection Failed', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get connection status message
     */
    public function getConnectionStatus(): array
    {
        if ($this->checkConnection()) {
            return [
                'connected' => true,
                'message' => 'เชื่อมต่อกับฐานข้อมูล Face Attendance สำเร็จ',
            ];
        }

        return [
            'connected' => false,
            'message' => 'ไม่สามารถเชื่อมต่อกับฐานข้อมูล Face Attendance ได้',
        ];
    }

    /**
     * Convert photo path to full URL for Face Attendance storage
     * 
     * @param string|null $path
     * @return string|null
     */
    protected function getPhotoUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // If already a full URL, return as is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Get storage URL from config
        $storageUrl = config('services.face_attendance.storage_url', 'https://nass.ac.th/faceattendance/storage');
        
        // Remove leading slash if present
        $path = ltrim($path, '/');
        
        return rtrim($storageUrl, '/') . '/' . $path;
    }
}
