<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhuongTien extends Model
{
    protected $table = 'phuong_tien';
    public $timestamps = false;
    protected $fillable = [
        'ten_phuong_tien', 'bien_so', 'loai_phuong_tien', 'can_ho',
        'ngay_dang_ky', 'ngay_huy', 'trang_thai', 'nguoi_cap_nhat',
    ];

    protected $casts = [
        'ngay_dang_ky' => 'datetime',
        'ngay_huy' => 'datetime',
    ];

    public function loaiPhuongTien()
    {
        return $this->belongsTo(LoaiPhuongTien::class, 'loai_phuong_tien');
    }

    public function canHo()
    {
        return $this->belongsTo(CanHo::class, 'can_ho');
    }
}
