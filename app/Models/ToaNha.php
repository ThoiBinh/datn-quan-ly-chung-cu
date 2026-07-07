<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToaNha extends Model
{
    use SoftDeletes;

    protected $table = 'toa_nha';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';
    protected $fillable = ['ten_toa_nha', 'dia_chi', 'so_tang'];

    public function canHo()
    {
        return $this->hasMany(CanHo::class, 'toa_nha');
    }
}
