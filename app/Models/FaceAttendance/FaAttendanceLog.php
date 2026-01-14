<?php

namespace App\Models\FaceAttendance;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for AttendanceLog from Face Attendance database
 */
class FaAttendanceLog extends Model
{
    protected $connection = 'face_attendance';
    protected $table = 'attendance_logs';

    protected $fillable = [
        'employee_id',
        'device_id',
        'scan_type',
        'scan_time',
        'is_late',
        'confidence_score',
        'snapshot_path',
    ];

    protected $casts = [
        'scan_time' => 'datetime',
        'is_late' => 'boolean',
    ];

    /**
     * Get employee
     */
    public function employee()
    {
        return $this->belongsTo(FaEmployee::class, 'employee_id');
    }
}
