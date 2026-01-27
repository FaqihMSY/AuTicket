<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function auditor(): HasOne
    {
        return $this->hasOne(Auditor::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPengawas(): bool
    {
        return $this->role === 'pengawas';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isAuditor(): bool
    {
        return $this->auditor()->exists();
    }

    public function canManageProjects(): bool
    {
        return in_array($this->role, ['admin', 'pengawas']);
    }
}
