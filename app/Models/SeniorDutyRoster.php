<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeniorDutyRoster extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'senior_officer_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * นายทหารเวรอาวุโส
     */
    public function seniorOfficer()
    {
        return $this->belongsTo(User::class, 'senior_officer_id');
    }

    /**
     * ผู้บันทึก
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Filter records that overlap with a given month
     */
    public function scopeForMonth($query, $year, $month)
    {
        $startOfMonth = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        return $query->where('start_date', '<=', $endOfMonth)
            ->where('end_date', '>=', $startOfMonth);
    }

    /**
     * Check if this roster covers a specific date
     */
    public function coversDate($date): bool
    {
        $date = \Carbon\Carbon::parse($date);
        return $date->between($this->start_date, $this->end_date);
    }
}
