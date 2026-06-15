<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiHopDong extends Model
{
    protected $table = 'loai_hop_dong';
    public $timestamps = false;
    protected $fillable = ['ten_loai'];
}
