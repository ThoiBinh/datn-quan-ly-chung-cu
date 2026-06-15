<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NguonTao extends Model
{
    protected $table = 'nguon_tao';
    public $timestamps = false;
    protected $fillable = ['ten_nguon_tao'];

    const ADMIN = 1;
    const RESIDENT_APP = 2;
    const MOMO = 3;
    const VNPAY = 4;
}
