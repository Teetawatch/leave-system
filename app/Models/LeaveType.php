<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'slug', 
        'max_days_per_year', 
        'requires_advance_notice', 
        'advance_notice_days', 
        'enforce_advance_notice',
        'allows_retroactive', 
        'enforce_retroactive_check',
        'max_retroactive_days',
        'enforce_balance_check',
        'requires_file'
    ];

    protected $casts = [
        'requires_advance_notice' => 'boolean',
        'enforce_advance_notice' => 'boolean',
        'allows_retroactive' => 'boolean',
        'enforce_retroactive_check' => 'boolean',
        'enforce_balance_check' => 'boolean',
        'requires_file' => 'boolean',
    ];
}
