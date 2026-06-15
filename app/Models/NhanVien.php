<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class NhanVien extends Authenticatable
{
    protected $table = 'nhan_vien';

    protected $fillable = [
        'ho_ten', 'chuc_vu', 'sdt', 'email', 'mat_khau',
        'trang_thai', 'ma_nhan_vien', 'cccd',
    ];

    protected $hidden = ['mat_khau', 'remember_token'];

    // Ánh xạ field password cho Laravel Auth
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    // Xác định vai trò dựa vào chuc_vu
    public function getVaitroAttribute(): string
    {
        // chuc_vu id=3 là Admin, còn lại là manager
        return $this->chuc_vu == 3 ? 'admin' : 'manager';
    }

    public function isActive(): bool
    {
        return $this->trang_thai == 1;
    }

    public function chucVu()
    {
        return $this->belongsTo(ChucVu::class, 'chuc_vu');
    }
}
