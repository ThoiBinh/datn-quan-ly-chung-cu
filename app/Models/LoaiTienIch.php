<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoaiTienIch extends Model
{
    use SoftDeletes;

    protected $table = 'loai_tien_ich';
    public $timestamps = false;

    const DELETED_AT = 'deletedAt';

    protected $fillable = ['ten_loai_tien_ich'];

    public function tienIch()
    {
        return $this->hasMany(TienIch::class, 'loai_tien_ich');
    }
}
