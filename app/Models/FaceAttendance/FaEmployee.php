<?php

namespace App\Models\FaceAttendance;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for Employee from Face Attendance database
 */
class FaEmployee extends Model
{
    protected $connection = 'face_attendance';
    protected $table = 'employees';

    protected $fillable = [
        'employee_code',
        'user_id',
        'first_name',
        'last_name',
        'department',
        'position',
        'shift_id',
        'photo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Attendance logs
     */
    public function attendanceLogs()
    {
        return $this->hasMany(FaAttendanceLog::class, 'employee_id');
    }
}
