<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiTinhPhiDichVu extends Model
{
    protected $table = 'loai_tinh_phi_dich_vu';
    public $timestamps = false;
    protected $fillable = ['ten_loai'];

    const THEO_DAU_NGUOI = 1;
    const THEO_CHI_SO = 2;
    const THEO_PHUONG_TIEN = 3;
}
