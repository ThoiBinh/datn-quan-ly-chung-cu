<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\HoaDon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class NhanVien extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'nhan_vien';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    public const TRANG_THAI_DANG_LAM = 1;
    public const TRANG_THAI_DA_NGHI  = 0;

    protected $fillable = [
        'ho_ten', 'chuc_vu', 'sdt', 'email',
        // mat_khau: PBKDF2-SHA256, 100,000 iterations, 16-byte salt, 32-byte key via Hash::make().
        // Legacy bcrypt hashes ($2y$12$...) are auto-upgraded to PBKDF2 on first login
        // via App\Auth\NhanVienUserProvider::validateCredentials().
        'mat_khau',
        'trang_thai', 'ma_nhan_vien', 'cccd',
        'ngay_sinh', 'ngay_vao_lam', 'ngay_nghi_lam', 'ghi_chu', 'nguoi_cap_nhat',
    ];

    protected $hidden = ['mat_khau'];

    protected $rememberTokenName = null;

    protected $casts = [
        'ngay_sinh'    => 'datetime',
        'ngay_vao_lam' => 'datetime',
        'ngay_nghi_lam' => 'datetime',
    ];

    public function getAuthPassword(): string
    {
        return $this->mat_khau;
    }

    public function getNameAttribute(): string
    {
        return $this->ho_ten;
    }

    public function getVaitroAttribute(): string
    {
        return $this->isAdmin() ? 'admin' : 'manager';
    }

    public function getRoleAttribute(): string
    {
        return $this->vaitro;
    }

    public function isAdmin(): bool
    {
        return $this->chucVu?->chuc_vu === ChucVu::ROLE_ADMIN;
    }

    /**
     * True nếu nhân viên này có chức vụ Admin hoặc Quản lý — tức là ngoài tầm
     * xem/sửa/xóa của một Manager (chỉ được thao tác trên nhân viên cấp dưới).
     */
    public function isRestrictedForManager(): bool
    {
        return in_array($this->chucVu?->chuc_vu, ChucVu::restrictedRoleNames(), true);
    }

    /**
     * Chỉ lấy các nhân viên mà Manager được phép nhìn thấy (loại Admin và Quản lý).
     * Dùng chung cho index/search/filter/thống kê để đảm bảo số liệu luôn khớp nhau.
     */
    public function scopeVisibleToManager($query)
    {
        return $query->whereHas('chucVu', function ($q) {
            $q->whereNotIn('chuc_vu', ChucVu::restrictedRoleNames());
        });
    }

    // Accessor để view dùng $user->status hoạt động như với User model
    public function getStatusAttribute(): string
    {
        return $this->trang_thai == self::TRANG_THAI_DANG_LAM ? 'active' : 'inactive';
    }

    // Alias phone → sdt
    public function getPhoneAttribute(): ?string
    {
        return $this->sdt;
    }

    public function isActive(): bool
    {
        return $this->trang_thai == self::TRANG_THAI_DANG_LAM;
    }

    public function getCreatedAtAttribute(): ?Carbon
    {
        return isset($this->attributes['createdAt']) && $this->attributes['createdAt']
            ? Carbon::parse($this->attributes['createdAt'])
            : null;
    }

    public function chucVu()
    {
        return $this->belongsTo(ChucVu::class, 'chuc_vu');
    }

    public function hoaDon()
    {
        return $this->hasMany(HoaDon::class, 'nguoi_cap_nhat');
    }

    public function lichSuThanhToan()
    {
        return $this->hasManyThrough(
            \App\Models\LichSuThanhToan::class,
            HoaDon::class,
            'nguoi_cap_nhat',
            'hoa_don'
        );
    }

    public function yeuCauXuLy()
    {
        return $this->hasMany(\App\Models\YeuCauCuDan::class, 'nhan_vien_xu_ly');
    }

    public function thongBao()
    {
        return $this->hasMany(\App\Models\ThongBao::class, 'nguoi_tao');
    }

    public function bangTin()
    {
        return $this->hasMany(\App\Models\BangTin::class, 'nguoi_tao');
    }

    public function nguoiCapNhat()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_cap_nhat')->withTrashed();
    }
}
