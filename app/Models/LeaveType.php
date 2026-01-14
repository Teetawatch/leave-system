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
        'allows_retroactive', 
        'requires_file'
    ];
}
