<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhuongTien extends Model
{
    use SoftDeletes;

    protected $table = 'phuong_tien';
    public $timestamps = false;

    const DELETED_AT = 'deletedAt';

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
        return $this->belongsTo(LoaiPhuongTien::class, 'loai_phuong_tien')->withTrashed();
    }

    public function canHo()
    {
        return $this->belongsTo(CanHo::class, 'can_ho')->withTrashed();
    }

    public function nguoiCapNhat()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_cap_nhat')->withTrashed();
    }

    public function getTrangThaiLabelAttribute(): array
    {
        return $this->trang_thai == 1
            ? ['text' => 'Đang sử dụng', 'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400']
            : ['text' => 'Đã hủy', 'class' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'];
    }
}
