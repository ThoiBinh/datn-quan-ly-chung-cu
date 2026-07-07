<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanHoPhiDichVu extends Model
{
    protected $table = 'can_ho_phi_dich_vu';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'can_ho', 'phi_dich_vu', 'don_gia', 'nguoi_cap_nhat',
    ];

    protected $casts = [
        'don_gia' => 'decimal:2',
    ];

    public function canHo()
    {
        return $this->belongsTo(CanHo::class, 'can_ho')->withTrashed();
    }

    public function phiDichVu()
    {
        return $this->belongsTo(PhiDichVu::class, 'phi_dich_vu')->withTrashed();
    }

    public function nguoiCapNhat()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_cap_nhat')->withTrashed();
    }
}
