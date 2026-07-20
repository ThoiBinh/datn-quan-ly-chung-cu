<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrangThaiCanHo extends Model
{
    use SoftDeletes;

    protected $table = 'trang_thai_can_ho';
    public $timestamps = false;

    const DELETED_AT = 'deletedAt';

    protected $fillable = ['ten_trang_thai'];

    public function canHo()
    {
        return $this->hasMany(CanHo::class, 'trang_thai');
    }
}
