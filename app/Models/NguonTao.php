<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NguonTao extends Model
{
    use SoftDeletes;

    protected $table = 'nguon_tao';
    public $timestamps = false;

    const DELETED_AT = 'deletedAt';

    protected $fillable = ['ten_nguon_tao'];

    const ADMIN = 1;
    const RESIDENT_APP = 2;
    const MOMO = 3;
    const VNPAY = 4;
}
