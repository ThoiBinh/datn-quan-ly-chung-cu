<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoaiTinhPhiDichVu extends Model
{
    use SoftDeletes;

    protected $table = 'loai_tinh_phi_dich_vu';
    public $timestamps = false;

    const DELETED_AT = 'deletedAt';

    protected $fillable = ['ten_loai'];

    const THEO_DAU_NGUOI = 1;
    const THEO_CHI_SO = 2;
    const THEO_PHUONG_TIEN = 3;
}
