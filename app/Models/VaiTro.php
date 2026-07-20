<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VaiTro extends Model
{
    use SoftDeletes;

    protected $table = 'vai_tro';
    public $timestamps = false;

    const DELETED_AT = 'deletedAt';

    protected $fillable = ['vai_tro'];

    public function cuDanCanHo()
    {
        return $this->hasMany(CuDanCanHo::class, 'vai_tro');
    }
}
