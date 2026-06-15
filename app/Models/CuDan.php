<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class CuDan extends Authenticatable
{
    protected $table = 'cu_dan';

    protected $fillable = [
        'ho_ten_dem', 'ten', 'sdt', 'cccd', 'email',
        'ngay_sinh', 'gioi_tinh', 'tinh', 'xa', 'dia_chi',
        'mat_khau', 'tai_khoan_kich_hoat',
    ];

    protected $hidden = ['mat_khau', 'remember_token'];

    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    // Họ tên đầy đủ
    public function getHoTenAttribute(): string
    {
        return $this->ho_ten_dem . ' ' . $this->ten;
    }

    public function isActive(): bool
    {
        return $this->tai_khoan_kich_hoat == 1;
    }

    public function cuDanCanHo()
    {
        return $this->hasMany(CuDanCanHo::class, 'cu_dan');
    }

    public function canHoHienTai()
    {
        return $this->hasOne(CuDanCanHo::class, 'cu_dan')->where('trang_thai', 1);
    }

    public function hopDong()
    {
        return $this->hasMany(HopDong::class, 'cu_dan');
    }

    public function yeuCau()
    {
        return $this->hasMany(YeuCauCuDan::class, 'cu_dan');
    }
}
