<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThuocTinh extends Model
{
    use SoftDeletes;

    protected $table = 'thuoc_tinh';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $fillable = ['ten_thuoc_tinh'];

    public function canHo()
    {
        return $this->belongsToMany(CanHo::class, 'thuoc_tinh_can_ho', 'thuoc_tinh', 'can_ho')
            ->withPivot('gia_tri_thuoc_tinh', 'kieu_du_lieu')->withTimestamps('createdAt', 'updatedAt');
    }
}
