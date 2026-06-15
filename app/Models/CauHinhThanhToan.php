<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CauHinhThanhToan extends Model
{
    protected $table = 'cau_hinh_thanh_toan';
    protected $fillable = [
        'loai_phuong_thuc', 'ten_nha_cung_cap', 'dinh_danh_thu_huong',
        'ma_nhan_dien', 'ten_chu_tai_khoan', 'trang_thai',
    ];
}
