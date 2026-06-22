<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BangTin extends Model
{
    use SoftDeletes;

    protected $table = 'bang_tin';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $fillable = [
        'tieu_de', 'noi_dung', 'hinh_url', 'nguoi_tao', 'nguoi_cap_nhat',
    ];

    public function nguoiTao()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_tao');
    }

    public function nguoiCapNhat()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_cap_nhat');
    }
}
