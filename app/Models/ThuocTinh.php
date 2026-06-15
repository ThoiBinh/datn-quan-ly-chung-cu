<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThuocTinh extends Model
{
    protected $table = 'thuoc_tinh';
    public $timestamps = false;
    protected $fillable = ['ten_thuoc_tinh'];

    public function canHo()
    {
        return $this->belongsToMany(CanHo::class, 'thuoc_tinh_can_ho', 'thuoc_tinh', 'can_ho')
            ->withPivot('gia_tri_thuoc_tinh')->withTimestamps();
    }
}
