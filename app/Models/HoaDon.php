<?php

namespace App\Models;

use App\Models\NhanVien;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HoaDon extends Model
{
    use SoftDeletes;

    protected $table = 'hoa_don';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $fillable = [
        'ma_thanh_toan', 'can_ho', 'thang', 'nam', 'tong_tien',
        'so_tien_da_thanh_toan', 'chi_phi', 'han_thanh_toan', 'trang_thai', 'nguoi_cap_nhat',
    ];

    protected $casts = [
        'tong_tien' => 'decimal:2',
        'so_tien_da_thanh_toan' => 'decimal:2',
        'chi_phi' => 'decimal:2',
        'han_thanh_toan' => 'datetime',
    ];

    const TRANG_THAI_CHUA_THANH_TOAN = 1;
    const TRANG_THAI_DA_THANH_TOAN = 2;
    const TRANG_THAI_QUA_HAN = 3;
    const TRANG_THAI_DA_HUY = 4;

    public function canHo()
    {
        return $this->belongsTo(CanHo::class, 'can_ho')->withTrashed();
    }

    public function chiTiet()
    {
        return $this->hasMany(ChiTietHoaDon::class, 'hoa_don');
    }

    public function lichSuThanhToan()
    {
        return $this->hasMany(LichSuThanhToan::class, 'hoa_don');
    }

    public function nguoiCapNhat()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_cap_nhat')->withTrashed();
    }

    public function conNo(): float
    {
        return max(0, (float)$this->tong_tien - (float)$this->so_tien_da_thanh_toan);
    }

    public function scopeChuaHuy($query)
    {
        return $query->where('trang_thai', '!=', self::TRANG_THAI_DA_HUY);
    }

    public function scopeSelectSumDuNo($query, string $as = 'du_no')
    {
        return $query->selectRaw(
            "SUM(CASE WHEN tong_tien > so_tien_da_thanh_toan THEN tong_tien - so_tien_da_thanh_toan ELSE 0 END) as {$as}"
        );
    }

    public function getTrangThaiLabelAttribute(): string
    {
        return match($this->trang_thai) {
            1 => 'Chưa thanh toán',
            2 => 'Đã thanh toán',
            3 => 'Trễ hạn',
            default => 'Không xác định',
        };
    }
}
