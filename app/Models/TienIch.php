<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TienIch extends Model
{
    use SoftDeletes;

    protected $table = 'tien_ich';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $fillable = [
        'ten_tien_ich', 'loai_tien_ich', 'toa_nha', 'mo_ta', 'vi_tri',
        'suc_chua', 'gio_mo_cua', 'gio_dong_cua', 'phi_su_dung',
        'can_dat_truoc', 'hinh_url', 'trang_thai', 'nguoi_cap_nhat',
    ];

    protected $casts = [
        'phi_su_dung'   => 'decimal:2',
        'can_dat_truoc' => 'boolean',
    ];

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
