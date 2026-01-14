<?php

namespace App\Models\FaceAttendance;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for StudentAttendanceLog from Face Attendance database
 */
class FaStudentAttendanceLog extends Model
{
    protected $connection = 'face_attendance';
    protected $table = 'student_attendance_logs';

    protected $fillable = [
        'student_id',
        'device_id',
        'scan_type',
        'period',
        'is_late',
        'scan_time',
        'snapshot_path',
    ];

    protected $casts = [
        'scan_time' => 'datetime',
        'is_late' => 'boolean',
    ];

    /**
     * Get student
     */
    public function student()
    {
        return $this->belongsTo(FaStudent::class, 'student_id');
    }
}
