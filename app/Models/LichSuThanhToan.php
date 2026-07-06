<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuThanhToan extends Model
{
    protected $table = 'lich_su_thanh_toan';
    public $timestamps = false;

    protected $fillable = [
        'hoa_don', 'ngay_thanh_toan', 'so_tien', 'phuong_thuc_thanh_toan',
        'ma_giao_dich', 'nguoi_thanh_toan', 'ghi_chu', 'nguon_tao', 'createdAt',
    ];

    protected $casts = [
        'ngay_thanh_toan' => 'datetime',
        'so_tien' => 'decimal:2',
        'createdAt' => 'datetime',
    ];

    public function hoaDon()
    {
        return $this->belongsTo(HoaDon::class, 'hoa_don')->withTrashed();
    }

    public function nguoiThanhToan()
    {
        return $this->belongsTo(CuDan::class, 'nguoi_thanh_toan');
    }

    public function nguonTao()
    {
        return $this->belongsTo(NguonTao::class, 'nguon_tao')->withTrashed();
    }
}
