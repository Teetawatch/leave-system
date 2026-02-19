<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'line_user_id',
        'password',
        'role',
        'department',
        'position',
        'rank',
        'start_date',
        'supervisor_id',
        'deputy_id',
        'manager_id',
        'fcm_token',
        'avatar',
        'signature',
        'is_registered',
        'registration_status',
    ];

    /**
     * Scope for unregistered employees (imported but no email/password yet)
     */
    public function scopeUnregistered($query)
    {
        return $query->where('is_registered', false);
    }

    /**
     * Scope for registered employees
     */
    public function scopeRegistered($query)
    {
        return $query->where('is_registered', true);
    }

    /**
     * Scope for pending registration approval
     */
    public function scopePendingApproval($query)
    {
        return $query->where('registration_status', 'pending');
    }

    /**
     * Scope for approved employees (can login)
     */
    public function scopeApproved($query)
    {
        return $query->where('registration_status', 'approved');
    }

    /**
     * Check if user can login
     */
    public function canLogin(): bool
    {
        return $this->is_registered && $this->registration_status === 'approved';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'start_date' => 'date',
    ];

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function deputy()
    {
        return $this->belongsTo(User::class, 'deputy_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
