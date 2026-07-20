<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietHoaDon extends Model
{
    protected $table = 'chi_tiet_hoa_don';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'hoa_don', 'ten_phi_dich_vu', 'don_gia', 'chi_so_cu', 'chi_so_moi', 'so_luong', 'thanh_tien',
    ];

    protected $casts = [
        'don_gia'    => 'decimal:2',
        'so_luong'   => 'decimal:2',
        'thanh_tien' => 'decimal:2',
    ];

    public function hoaDon()
    {
        return $this->belongsTo(HoaDon::class, 'hoa_don')->withTrashed();
    }
}
