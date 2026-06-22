<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
}
