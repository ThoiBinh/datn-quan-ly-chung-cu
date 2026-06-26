<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonViTinhPhiDichVu extends Model
{
    protected $table = 'don_vi_tinh_phi_dich_vu';
    public $timestamps = false;
    protected $fillable = ['don_vi'];

    public function phiDichVu()
    {
        return $this->hasMany(PhiDichVu::class, 'don_vi_tinh');
    }
}
