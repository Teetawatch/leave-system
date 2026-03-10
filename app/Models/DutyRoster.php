<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DutyRoster extends Model
{
    use HasFactory;

    protected $fillable = [
        'duty_date',
        'duty_officer_id',
        'reserve_duty_officer_id',
        'assistant_duty_officer_id',
        'reserve_assistant_duty_officer_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'duty_date' => 'date',
    ];

    /**
     * นายทหารเวร
     */
    public function dutyOfficer()
    {
        return $this->belongsTo(User::class, 'duty_officer_id');
    }

    /**
     * นายทหารเวรสำรอง
     */
    public function reserveDutyOfficer()
    {
        return $this->belongsTo(User::class, 'reserve_duty_officer_id');
    }

    /**
     * ผู้ช่วยนายทหารเวร
     */
    public function assistantDutyOfficer()
    {
        return $this->belongsTo(User::class, 'assistant_duty_officer_id');
    }

    /**
     * ผู้ช่วยนายทหารเวรสำรอง
     */
    public function reserveAssistantDutyOfficer()
    {
        return $this->belongsTo(User::class, 'reserve_assistant_duty_officer_id');
    }

    /**
     * ผู้บันทึก
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Filter by month and year
     */
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('duty_date', $year)
            ->whereMonth('duty_date', $month);
    }
}
