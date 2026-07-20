<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonViTinhPhiDichVu extends Model
{
    use SoftDeletes;

    protected $table = 'don_vi_tinh_phi_dich_vu';
    public $timestamps = false;

    const DELETED_AT = 'deletedAt';

    protected $fillable = ['don_vi'];

    public function phiDichVu()
    {
        return $this->hasMany(PhiDichVu::class, 'don_vi_tinh');
    }
}
