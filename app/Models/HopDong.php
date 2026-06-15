<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HopDong extends Model
{
    protected $table = 'hop_dong';
    protected $fillable = [
        'so_hop_dong', 'can_ho', 'cu_dan', 'loai_hop_dong', 'ngay_ky',
        'ngay_bat_dau', 'ngay_ket_thuc', 'gia_tri_hop_dong', 'tien_coc',
        'file_dinh_kem', 'ghi_chu', 'trang_thai', 'nguoi_tao',
    ];

    protected $casts = [
        'ngay_ky' => 'datetime',
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
        'gia_tri_hop_dong' => 'decimal:2',
        'tien_coc' => 'decimal:2',
    ];

    const DANG_HIEU_LUC = 1;
    const HET_HAN = 2;
    const DA_THANH_LY = 3;
    const CHO_KY = 4;

    public function canHo()
    {
        return $this->belongsTo(CanHo::class, 'can_ho');
    }

    public function cuDan()
    {
        return $this->belongsTo(CuDan::class, 'cu_dan');
    }

    public function loaiHopDong()
    {
        return $this->belongsTo(LoaiHopDong::class, 'loai_hop_dong');
    }

    public function nguoiTao()
    {
        return $this->belongsTo(User::class, 'nguoi_tao');
    }

    public function getTrangThaiLabelAttribute(): string
    {
        return match($this->trang_thai) {
            1 => 'Đang hiệu lực',
            2 => 'Đã hết hạn',
            3 => 'Đã thanh lý',
            4 => 'Chờ ký',
            default => 'Không xác định',
        };
    }
}
