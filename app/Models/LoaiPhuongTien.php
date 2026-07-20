<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoaiPhuongTien extends Model
{
    use SoftDeletes;

    protected $table = 'loai_phuong_tien';
    public $timestamps = false;

    const DELETED_AT = 'deletedAt';

    protected $fillable = ['ten_loai_phuong_tien'];

    public function phuongTien()
    {
        return $this->hasMany(PhuongTien::class, 'loai_phuong_tien');
    }
}
