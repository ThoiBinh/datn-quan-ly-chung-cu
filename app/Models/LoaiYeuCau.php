<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoaiYeuCau extends Model
{
    use SoftDeletes;

    protected $table = 'loai_yeu_cau';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $fillable = ['name', 'nguoi_cap_nhat'];

    public function yeuCau()
    {
        return $this->hasMany(YeuCauCuDan::class, 'loai_yeu_cau');
    }

    public function nguoiCapNhat()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_cap_nhat')->withTrashed();
    }
}
