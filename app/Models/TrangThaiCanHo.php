<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrangThaiCanHo extends Model
{
    protected $table = 'trang_thai_can_ho';
    public $timestamps = false;
    protected $fillable = ['ten_trang_thai'];

    public function canHo()
    {
        return $this->hasMany(CanHo::class, 'trang_thai');
    }
}
