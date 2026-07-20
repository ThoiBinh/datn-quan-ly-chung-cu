<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoaiCanHo extends Model
{
    use SoftDeletes;

    protected $table = 'loai_can_ho';
    public $timestamps = false;

    const DELETED_AT = 'deletedAt';

    protected $fillable = ['ten_loai_can_ho'];

    public function canHo()
    {
        return $this->hasMany(CanHo::class, 'loai_can_ho');
    }
}
