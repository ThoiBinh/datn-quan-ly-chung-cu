<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CauHinhThanhToan extends Model
{
    use SoftDeletes;

    protected $table = 'cau_hinh_thanh_toan';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';
    protected $fillable = [
        'loai_phuong_thuc', 'ten_nha_cung_cap', 'dinh_danh_thu_huong',
        'ma_nhan_dien', 'ten_chu_tai_khoan', 'trang_thai', 'nguoi_cap_nhat',
    ];

    public function nguoiCapNhat()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_cap_nhat')->withTrashed();
    }
}
