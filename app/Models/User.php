<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'status', 'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isResident(): bool
    {
        return $this->role === 'resident';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function cuDan()
    {
        return $this->hasOne(CuDan::class, 'user_id');
    }

    public function thongBaoTao()
    {
        return $this->hasMany(ThongBao::class, 'nguoi_tao');
    }

    public function nhatKy()
    {
        return $this->hasMany(NhatKyHeThong::class, 'nguoi_thuc_hien');
    }
}
