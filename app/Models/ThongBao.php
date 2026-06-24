<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThongBao extends Model
{
    use SoftDeletes;

    protected $table = 'thong_bao';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = ['tieu_de', 'noi_dung', 'nguoi_tao'];

    const DELETED_AT = 'deletedAt';

    public function getCreatedAtAttribute(): ?Carbon
    {
        return isset($this->attributes['createdAt']) && $this->attributes['createdAt']
            ? Carbon::parse($this->attributes['createdAt'])
            : null;
    }

    public function nguoiTao()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_tao');
    }

    public function daDoc()
    {
        return $this->hasMany(ThongBaoDaDoc::class, 'thong_bao_id');
    }
}
