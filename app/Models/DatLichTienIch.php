<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatLichTienIch extends Model
{
    use SoftDeletes;

    protected $table = 'dat_lich_tien_ich';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $fillable = [
        'ma_dat_lich', 'cu_dan', 'can_ho', 'tien_ich', 'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc', 'so_nguoi', 'phi_su_dung', 'ghi_chu', 'trang_thai',
        'nhan_vien_duyet', 'ngay_duyet', 'ngay_huy', 'ly_do_huy', 'nguoi_cap_nhat',
    ];

    protected $casts = [
        'thoi_gian_bat_dau'  => 'datetime',
        'thoi_gian_ket_thuc' => 'datetime',
        'phi_su_dung'        => 'decimal:2',
        'ngay_duyet'         => 'datetime',
        'ngay_huy'           => 'datetime',
    ];

    const TRANG_THAI_CHO_DUYET = 1;
    const TRANG_THAI_DA_DUYET = 2;
    const TRANG_THAI_TU_CHOI = 3;
    const TRANG_THAI_DA_HUY = 4;
    const TRANG_THAI_HOAN_THANH = 5;

    public static function dsTrangThai(): array
    {
        return [
            self::TRANG_THAI_CHO_DUYET   => 'Chờ duyệt',
            self::TRANG_THAI_DA_DUYET    => 'Đã duyệt',
            self::TRANG_THAI_TU_CHOI     => 'Từ chối',
            self::TRANG_THAI_DA_HUY      => 'Đã hủy',
            self::TRANG_THAI_HOAN_THANH  => 'Hoàn thành',
        ];
    }

    public function getTrangThaiLabelAttribute(): array
    {
        return match ((int) $this->trang_thai) {
            self::TRANG_THAI_CHO_DUYET  => ['text' => 'Chờ duyệt', 'class' => 'bg-amber-100 text-amber-700'],
            self::TRANG_THAI_DA_DUYET   => ['text' => 'Đã duyệt', 'class' => 'bg-emerald-100 text-emerald-700'],
            self::TRANG_THAI_TU_CHOI    => ['text' => 'Từ chối', 'class' => 'bg-red-100 text-red-700'],
            self::TRANG_THAI_DA_HUY     => ['text' => 'Đã hủy', 'class' => 'bg-gray-100 text-gray-600'],
            self::TRANG_THAI_HOAN_THANH => ['text' => 'Hoàn thành', 'class' => 'bg-indigo-100 text-indigo-700'],
            default => ['text' => 'Không xác định', 'class' => 'bg-gray-100 text-gray-500'],
        };
    }

    public function cuDan()
    {
        return $this->belongsTo(CuDan::class, 'cu_dan');
    }

    public function canHo()
    {
        return $this->belongsTo(CanHo::class, 'can_ho')->withTrashed();
    }

    public function tienIch()
    {
        return $this->belongsTo(TienIch::class, 'tien_ich')->withTrashed();
    }

    public function nhanVienDuyet()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_duyet')->withTrashed();
    }

    public function nguoiCapNhat()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_cap_nhat')->withTrashed();
    }
}
