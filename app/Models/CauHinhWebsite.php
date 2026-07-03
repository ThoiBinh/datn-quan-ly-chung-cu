<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CauHinhWebsite extends Model
{
    protected $table = 'cau_hinh_website';

    protected $fillable = [
        'ma_thuoc_tinh',
        'ten_thuoc_tinh',
        'gia_tri',
        'kieu_du_lieu',
        'ma_nhom',
        'ten_nhom',
        'mo_ta',
        'placeholder',
        'thu_tu',
        'la_bao_mat',
        'duoc_chinh_sua',
        'trang_thai',
    ];

    protected $casts = [
        'thu_tu'         => 'integer',
        'la_bao_mat'     => 'boolean',
        'duoc_chinh_sua' => 'boolean',
        'trang_thai'     => 'boolean',
    ];

    public const KIEU_DU_LIEU_OPTIONS = [
        'text', 'textarea', 'number', 'email', 'phone', 'url',
        'image', 'file', 'password', 'boolean', 'json',
    ];

    public const NHOM_OPTIONS = [
        'general' => 'Thông tin chung',
        'contact' => 'Liên hệ',
        'social'  => 'Mạng xã hội',
        'payment' => 'Thanh toán',
    ];

    public const UPLOAD_TYPES = ['image', 'file'];

    public function setMaNhomAttribute(string $value): void
    {
        $this->attributes['ma_nhom']  = $value;
        $this->attributes['ten_nhom'] = self::NHOM_OPTIONS[$value] ?? $value;
    }

    public function getGiaTriUrlAttribute(): ?string
    {
        if (in_array($this->kieu_du_lieu, self::UPLOAD_TYPES) && $this->gia_tri) {
            if (Storage::disk('public')->exists($this->gia_tri)) {
                return Storage::disk('public')->url($this->gia_tri);
            }
        }
        return null;
    }

    public function getGiaTriHienThiAttribute(): ?string
    {
        if ($this->la_bao_mat && $this->gia_tri) {
            return strlen($this->gia_tri) > 4
                ? '••••••••••••••••' . substr($this->gia_tri, -4)
                : '••••••••';
        }
        return $this->gia_tri;
    }

    public function scopePayment($query)
    {
        return $query->where('ma_nhom', 'payment');
    }

    /**
     * Lấy toàn bộ cấu hình của một nhóm dạng [ma_thuoc_tinh => gia_tri] bằng 1 query duy nhất.
     */
    public static function layNhom(string $maNhom): array
    {
        return static::query()->where('ma_nhom', $maNhom)->pluck('gia_tri', 'ma_thuoc_tinh')->all();
    }
}
