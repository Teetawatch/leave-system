<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'leave_request_id', 
        'approver_id', 
        'step', 
        'action', 
        'comment', 
        'signature',
        'ip_address'
    ];

    public function request()
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_request_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
