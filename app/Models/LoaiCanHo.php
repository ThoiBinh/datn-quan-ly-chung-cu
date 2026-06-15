<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiCanHo extends Model
{
    protected $table = 'loai_can_ho';
    public $timestamps = false;
    protected $fillable = ['ten_loai_can_ho'];

    public function canHo()
    {
        return $this->hasMany(CanHo::class, 'loai_can_ho');
    }
}
