<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThongBao extends Model
{
    use SoftDeletes;

    protected $table = 'thong_bao';
    protected $fillable = ['tieu_de', 'noi_dung', 'nguoi_tao'];

    const DELETED_AT = 'deletedAt';

    public function nguoiTao()
    {
        return $this->belongsTo(User::class, 'nguoi_tao');
    }
}
