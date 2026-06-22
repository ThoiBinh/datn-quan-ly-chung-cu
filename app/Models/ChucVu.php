<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChucVu extends Model
{
    use SoftDeletes;

    protected $table = 'chuc_vu';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $fillable = ['chuc_vu'];

    public function nhanVien()
    {
        return $this->hasMany(NhanVien::class, 'chuc_vu');
    }
}
