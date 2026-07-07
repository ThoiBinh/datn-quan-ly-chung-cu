<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class TienIch extends Model
{
    use SoftDeletes;

    protected $table = 'tien_ich';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    const TRANG_THAI_NGUNG_HOAT_DONG = 0;
    const TRANG_THAI_HOAT_DONG = 1;

    protected $fillable = [
        'ten_tien_ich', 'loai_tien_ich', 'toa_nha', 'mo_ta', 'vi_tri',
        'suc_chua', 'gio_mo_cua', 'gio_dong_cua', 'phi_su_dung',
        'can_dat_truoc', 'hinh_url', 'trang_thai', 'nguoi_cap_nhat',
    ];

    protected $casts = [
        'loai_tien_ich'  => 'integer',
        'toa_nha'        => 'integer',
        'suc_chua'       => 'integer',
        'phi_su_dung'    => 'decimal:2',
        'can_dat_truoc'  => 'boolean',
        'trang_thai'     => 'integer',
        'nguoi_cap_nhat' => 'integer',
    ];

    public static function dsTrangThai(): array
    {
        return [
            self::TRANG_THAI_HOAT_DONG        => 'Đang hoạt động',
            self::TRANG_THAI_NGUNG_HOAT_DONG   => 'Ngừng hoạt động',
        ];
    }

    /**
     * Nhãn hiển thị trạng thái hoạt động của tiện ích (dùng cho badge trên UI).
     */
    public function getTrangThaiLabelAttribute(): array
    {
        return match ((int) $this->trang_thai) {
            self::TRANG_THAI_HOAT_DONG      => ['text' => 'Đang hoạt động', 'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'],
            self::TRANG_THAI_NGUNG_HOAT_DONG => ['text' => 'Ngừng hoạt động', 'class' => 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-400'],
            default => ['text' => 'Không xác định', 'class' => 'bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-slate-400'],
        };
    }

    /**
     * Khung giờ hoạt động dạng "06:00 - 22:00", null nếu thiếu dữ liệu.
     */
    public function getGioHoatDongAttribute(): ?string
    {
        if (!$this->gio_mo_cua || !$this->gio_dong_cua) {
            return null;
        }

        return substr($this->gio_mo_cua, 0, 5).' - '.substr($this->gio_dong_cua, 0, 5);
    }

    /**
     * URL đầy đủ của hình tiện ích (qua Storage), null nếu chưa có ảnh hoặc file không tồn tại.
     */
    public function getHinhUrlFullAttribute(): ?string
    {
        if (!$this->hinh_url || !Storage::disk('public')->exists($this->hinh_url)) {
            return null;
        }

        return Storage::disk('public')->url($this->hinh_url);
    }

    /**
     * Chuẩn hoá tên tiện ích khi gán giá trị (tránh khoảng trắng thừa).
     */
    public function setTenTienIchAttribute(?string $value): void
    {
        $this->attributes['ten_tien_ich'] = $value !== null ? trim($value) : $value;
    }

    /**
     * Chỉ lấy các tiện ích đang hoạt động (trang_thai = 1).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('trang_thai', self::TRANG_THAI_HOAT_DONG);
    }

    public function loaiTienIch()
    {
        return $this->belongsTo(LoaiTienIch::class, 'loai_tien_ich')->withTrashed();
    }

    public function toaNha()
    {
        return $this->belongsTo(ToaNha::class, 'toa_nha')->withTrashed();
    }

    public function nguoiCapNhat()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_cap_nhat')->withTrashed();
    }

    public function datLich()
    {
        return $this->hasMany(DatLichTienIch::class, 'tien_ich');
    }
}
