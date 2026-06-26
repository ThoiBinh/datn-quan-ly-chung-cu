<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiPhuongTien extends Model
{
    protected $table = 'loai_phuong_tien';
    public $timestamps = false;
    protected $fillable = ['ten_loai_phuong_tien'];

    public function phuongTien()
    {
        return $this->hasMany(PhuongTien::class, 'loai_phuong_tien');
    }
}
