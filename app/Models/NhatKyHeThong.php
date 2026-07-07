<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhatKyHeThong extends Model
{
    protected $table = 'nhat_ky_he_thong';
    public $timestamps = false;

    protected $fillable = [
        'nguoi_thuc_hien', 'thoi_gian', 'hanh_dong', 'bang_tac_dong',
        'id_ban_ghi', 'gia_tri_cu', 'gia_tri_moi', 'createdAt',
    ];

    protected $casts = [
        'thoi_gian' => 'datetime',
        'createdAt' => 'datetime',
    ];

    public function nguoiThucHien()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_thuc_hien')->withTrashed();
    }
}
