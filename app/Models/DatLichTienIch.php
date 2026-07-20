<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'cu_dan'             => 'integer',
        'can_ho'             => 'integer',
        'tien_ich'           => 'integer',
        'thoi_gian_bat_dau'  => 'datetime',
        'thoi_gian_ket_thuc' => 'datetime',
        'so_nguoi'           => 'integer',
        'phi_su_dung'        => 'decimal:2',
        'trang_thai'         => 'integer',
        'nhan_vien_duyet'    => 'integer',
        'ngay_duyet'         => 'datetime',
        'ngay_huy'           => 'datetime',
        'nguoi_cap_nhat'     => 'integer',
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
            self::TRANG_THAI_CHO_DUYET  => ['text' => 'Chờ duyệt', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'],
            self::TRANG_THAI_DA_DUYET   => ['text' => 'Đã duyệt', 'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'],
            self::TRANG_THAI_TU_CHOI    => ['text' => 'Từ chối', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'],
            self::TRANG_THAI_DA_HUY     => ['text' => 'Đã hủy', 'class' => 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-400'],
            self::TRANG_THAI_HOAN_THANH => ['text' => 'Hoàn thành', 'class' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400'],
            default => ['text' => 'Không xác định', 'class' => 'bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-slate-400'],
        };
    }

    /**
     * Thời lượng sử dụng tiện ích tính theo phút, null nếu thiếu mốc thời gian.
     */
    public function getThoiLuongPhutAttribute(): ?int
    {
        if (!$this->thoi_gian_bat_dau || !$this->thoi_gian_ket_thuc) {
            return null;
        }

        return $this->thoi_gian_bat_dau->diffInMinutes($this->thoi_gian_ket_thuc);
    }

    /**
     * Chuẩn hoá mã đặt lịch khi gán giá trị (loại bỏ khoảng trắng thừa đầu/cuối).
     */
    public function setMaDatLichAttribute(?string $value): void
    {
        $this->attributes['ma_dat_lich'] = $value !== null ? trim($value) : $value;
    }

    /**
     * Các lượt đặt lịch còn hiệu lực: chờ duyệt, đã duyệt hoặc đã hoàn thành
     * (loại trừ những lượt đã bị từ chối hoặc đã hủy).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('trang_thai', [
            self::TRANG_THAI_TU_CHOI,
            self::TRANG_THAI_DA_HUY,
        ]);
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
