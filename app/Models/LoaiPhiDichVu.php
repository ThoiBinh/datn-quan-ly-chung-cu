<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoaiPhiDichVu extends Model
{
    use SoftDeletes;

    protected $table = 'loai_phi_dich_vu';
    public $timestamps = false;

    const DELETED_AT = 'deletedAt';

    protected $fillable = ['ten_loai_phi_dich_vu'];

    public function phiDichVu()
    {
        return $this->hasMany(PhiDichVu::class, 'loai_phi_dich_vu');
    }
}
