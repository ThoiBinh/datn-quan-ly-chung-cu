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

    public function nguoiCapNhat()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_cap_nhat');
    }

    public function getTrangThaiLabelAttribute(): array
    {
        return $this->trang_thai == 1
            ? ['text' => 'Đang sử dụng', 'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400']
            : ['text' => 'Đã hủy', 'class' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'];
    }
}
