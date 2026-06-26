<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    protected $table = 'vai_tro';
    public $timestamps = false;
    protected $fillable = ['vai_tro'];

    public function cuDanCanHo()
    {
        return $this->hasMany(CuDanCanHo::class, 'vai_tro');
    }
}
