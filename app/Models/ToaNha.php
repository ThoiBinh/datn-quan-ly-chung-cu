<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToaNha extends Model
{
    protected $table = 'toa_nha';
    protected $fillable = ['ten_toa_nha', 'dia_chi', 'so_tang'];

    public function canHo()
    {
        return $this->hasMany(CanHo::class, 'toa_nha');
    }
}
