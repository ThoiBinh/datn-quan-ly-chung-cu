<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuDanCanHo extends Model
{
    protected $table = 'cu_dan_can_ho';
    protected $fillable = [
        'cu_dan', 'can_ho', 'vai_tro', 'ngay_chuyen_den', 'ngay_chuyen_di', 'trang_thai',
    ];

    protected $casts = [
        'ngay_chuyen_den' => 'datetime',
        'ngay_chuyen_di' => 'datetime',
    ];

    public function cuDan()
    {
        return $this->belongsTo(CuDan::class, 'cu_dan');
    }

    public function canHo()
    {
        return $this->belongsTo(CanHo::class, 'can_ho');
    }

    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class, 'vai_tro');
    }
}
