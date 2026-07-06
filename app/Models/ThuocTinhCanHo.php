<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThuocTinhCanHo extends Model
{
    protected $table = 'thuoc_tinh_can_ho';

    // Composite primary key (can_ho, thuoc_tinh) — no auto-increment
    public $incrementing = false;
    protected $primaryKey = null;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'can_ho', 'thuoc_tinh', 'gia_tri_thuoc_tinh', 'kieu_du_lieu',
    ];

    // kieu_du_lieu: 1 = int, 2 = string, 3 = datetime
    protected $casts = [
        'kieu_du_lieu' => 'integer',
    ];

    public function canHo()
    {
        return $this->belongsTo(CanHo::class, 'can_ho')->withTrashed();
    }

    public function thuocTinh()
    {
        return $this->belongsTo(ThuocTinh::class, 'thuoc_tinh');
    }
}
