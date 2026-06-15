<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiPhiDichVu extends Model
{
    protected $table = 'loai_phi_dich_vu';
    public $timestamps = false;
    protected $fillable = ['ten_loai_phi_dich_vu'];

    public function phiDichVu()
    {
        return $this->hasMany(PhiDichVu::class, 'loai_phi_dich_vu');
    }
}
