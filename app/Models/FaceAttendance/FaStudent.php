<?php

namespace App\Models\FaceAttendance;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for Student from Face Attendance database
 */
class FaStudent extends Model
{
    protected $connection = 'face_attendance';
    protected $table = 'students';

    protected $fillable = [
        'student_code',
        'first_name',
        'last_name',
        'course_id',
        'class_year',
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
     * Get course
     */
    public function course()
    {
        return $this->belongsTo(FaCourse::class, 'course_id');
    }

    /**
     * Attendance logs
     */
    public function attendanceLogs()
    {
        return $this->hasMany(FaStudentAttendanceLog::class, 'student_id');
    }
}
