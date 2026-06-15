<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhiDichVu extends Model
{
    protected $table = 'phi_dich_vu';
    protected $fillable = [
        'loai_phi_dich_vu', 'ten_phi_dich_vu', 'don_gia', 'don_vi_tinh', 'loai_tinh_phi',
    ];

    protected $casts = ['don_gia' => 'decimal:2'];

    public function loaiPhiDichVu()
    {
        return $this->belongsTo(LoaiPhiDichVu::class, 'loai_phi_dich_vu');
    }

    public function donViTinh()
    {
        return $this->belongsTo(DonViTinhPhiDichVu::class, 'don_vi_tinh');
    }

    public function loaiTinhPhi()
    {
        return $this->belongsTo(LoaiTinhPhiDichVu::class, 'loai_tinh_phi');
    }

    public function canHo()
    {
        return $this->belongsToMany(CanHo::class, 'can_ho_phi_dich_vu', 'phi_dich_vu', 'can_ho')
            ->withPivot('don_gia')->withTimestamps();
    }
}
