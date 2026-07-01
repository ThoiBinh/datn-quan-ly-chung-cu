<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class CuDan extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'cu_dan';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $fillable = [
        'ho_ten_dem', 'ten', 'sdt', 'cccd', 'email',
        // mat_khau: PBKDF2-SHA256, 100,000 iterations, 16-byte salt, 32-byte key via Hash::make().
        // Format: PBKDF2$<iterations>$<base64_salt>$<base64_hash>
        // Driver: App\Hashing\Pbkdf2Hasher (registered in config/hashing.php)
        'mat_khau',
        'ngay_sinh', 'gioi_tinh', 'tinh', 'xa', 'dia_chi',
        'trang_thai', 'nguoi_cap_nhat',
    ];

    protected $hidden = ['mat_khau'];

    protected $rememberTokenName = null;

    protected $casts = [
        'ngay_sinh'  => 'date',
        'gioi_tinh'  => 'integer',
        'trang_thai' => 'integer',
    ];

    public function getAuthPassword(): string
    {
        return $this->mat_khau;
    }

    public function isActive(): bool
    {
        return $this->trang_thai == 1;
    }

    public function getHoTenAttribute(): string
    {
        return trim(($this->ho_ten_dem ?? '') . ' ' . ($this->ten ?? ''));
    }

    public function getNameAttribute(): string
    {
        return $this->ho_ten;
    }

    // Accessor để view dùng $model->created_at hoạt động với column createdAt
    public function getCreatedAtAttribute(): ?Carbon
    {
        return isset($this->attributes['createdAt']) && $this->attributes['createdAt']
            ? Carbon::parse($this->attributes['createdAt'])
            : null;
    }

    public function cuDanCanHo()
    {
        return $this->hasMany(CuDanCanHo::class, 'cu_dan');
    }

    public function canHoHienTai()
    {
        return $this->hasOne(CuDanCanHo::class, 'cu_dan')->where('trang_thai', 1);
    }

    public function yeuCau()
    {
        return $this->hasMany(YeuCauCuDan::class, 'cu_dan');
    }

    public function thongBaoDaDoc()
    {
        return $this->hasMany(ThongBaoDaDoc::class, 'cu_dan_id');
    }

    public function lichSuThanhToan()
    {
        return $this->hasMany(LichSuThanhToan::class, 'nguoi_thanh_toan');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            if (Storage::disk('public')->exists("avatars/{$this->id}.{$ext}")) {
                return Storage::disk('public')->url("avatars/{$this->id}.{$ext}");
            }
        }
        return null;
    }

    public function getTrangThaiLabelAttribute(): array
    {
        return match((int) $this->trang_thai) {
            1 => ['text' => 'Đang cư trú',  'class' => 'bg-emerald-100 text-emerald-700'],
            2 => ['text' => 'Tạm vắng',      'class' => 'bg-amber-100 text-amber-700'],
            3 => ['text' => 'Đã chuyển đi',  'class' => 'bg-gray-100 text-gray-600'],
            default => ['text' => 'Không xác định', 'class' => 'bg-gray-100 text-gray-500'],
        };
    }

    public function getGioiTinhLabelAttribute(): string
    {
        if ($this->gioi_tinh === null) {
            return '—';
        }
        return match((int) $this->gioi_tinh) {
            0 => 'Nữ',
            1 => 'Nam',
            default => '—',
        };
    }
}
