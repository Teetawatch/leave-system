<?php

namespace App\Models\FaceAttendance;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for Course from Face Attendance database
 */
class FaCourse extends Model
{
    protected $connection = 'face_attendance';
    protected $table = 'courses';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get students in this course
     */
    public function students()
    {
        return $this->hasMany(FaStudent::class, 'course_id');
    }
}
