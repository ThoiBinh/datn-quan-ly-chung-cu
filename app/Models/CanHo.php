<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanHo extends Model
{
    protected $table = 'can_ho';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = [
        'toa_nha', 'so_can_ho', 'tang', 'trang_thai', 'gia', 'loai_can_ho', 'nguoi_cap_nhat',
    ];

    protected $casts = [
        'gia' => 'decimal:2',
    ];

    public function toaNha()
    {
        return $this->belongsTo(ToaNha::class, 'toa_nha');
    }

    public function loaiCanHo()
    {
        return $this->belongsTo(LoaiCanHo::class, 'loai_can_ho');
    }

    public function trangThai()
    {
        return $this->belongsTo(TrangThaiCanHo::class, 'trang_thai');
    }

    public function cuDanCanHo()
    {
        return $this->hasMany(CuDanCanHo::class, 'can_ho');
    }

    public function cuDanHienTai()
    {
        return $this->hasMany(CuDanCanHo::class, 'can_ho')->where('trang_thai', 1);
    }

    public function phiDichVu()
    {
        return $this->belongsToMany(PhiDichVu::class, 'can_ho_phi_dich_vu', 'can_ho', 'phi_dich_vu')
            ->withPivot('don_gia')->withTimestamps('createdAt', 'updatedAt');
    }

    public function phuongTien()
    {
        return $this->hasMany(PhuongTien::class, 'can_ho');
    }

    public function hoaDon()
    {
        return $this->hasMany(HoaDon::class, 'can_ho');
    }

    public function thuocTinh()
    {
        return $this->belongsToMany(ThuocTinh::class, 'thuoc_tinh_can_ho', 'can_ho', 'thuoc_tinh')
            ->withPivot('gia_tri_thuoc_tinh', 'kieu_du_lieu')->withTimestamps('createdAt', 'updatedAt');
    }
}
