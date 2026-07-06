<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhiDichVu extends Model
{
    use SoftDeletes;

    protected $table = 'phi_dich_vu';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';
    protected $fillable = [
        'loai_phi_dich_vu', 'ten_phi_dich_vu', 'don_gia', 'don_vi_tinh', 'loai_tinh_phi', 'nguoi_cap_nhat',
    ];

    protected $casts = ['don_gia' => 'decimal:2'];

    public function loaiPhiDichVu()
    {
        return $this->belongsTo(LoaiPhiDichVu::class, 'loai_phi_dich_vu')->withTrashed();
    }

    public function donViTinh()
    {
        return $this->belongsTo(DonViTinhPhiDichVu::class, 'don_vi_tinh')->withTrashed();
    }

    public function loaiTinhPhi()
    {
        return $this->belongsTo(LoaiTinhPhiDichVu::class, 'loai_tinh_phi')->withTrashed();
    }

    public function canHo()
    {
        return $this->belongsToMany(CanHo::class, 'can_ho_phi_dich_vu', 'phi_dich_vu', 'can_ho')
            ->withPivot('don_gia')->withTimestamps('createdAt', 'updatedAt');
    }

    public function nguoiCapNhat()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_cap_nhat')->withTrashed();
    }
}
