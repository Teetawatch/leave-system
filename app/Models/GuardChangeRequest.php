<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuardChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'replacement_user_id',
        'duty_position',
        'duty_date',
        'remarks',
        'status',
        'approver_id',
        'approval_signature',
        'approval_comment',
        'approved_at',
        'director_approver_id',
        'director_signature',
        'director_comment',
        'director_approved_at',
        'final_approver_id',
        'final_signature',
        'final_comment',
        'final_approved_at',
    ];

    protected $casts = [
        'duty_date' => 'date',
        'approved_at' => 'datetime',
        'director_approved_at' => 'datetime',
        'final_approved_at' => 'datetime',
    ];

    /**
     * ผู้ขอเปลี่ยนยาม
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ผู้ที่จะมาเปลี่ยนแทน
     */
    public function replacementUser()
    {
        return $this->belongsTo(User::class, 'replacement_user_id');
    }

    /**
     * ผู้อนุมัติ (ผู้ที่มาเปลี่ยนแทน)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * ผู้อนุมัติระดับ รอง ผอ.
     */
    public function directorApprover()
    {
        return $this->belongsTo(User::class, 'director_approver_id');
    }

    /**
     * ผู้อนุมัติระดับ ผอ. (สุดท้าย)
     */
    public function finalApprover()
    {
        return $this->belongsTo(User::class, 'final_approver_id');
    }
}
