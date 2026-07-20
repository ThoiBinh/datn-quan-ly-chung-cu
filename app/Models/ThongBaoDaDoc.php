<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThongBaoDaDoc extends Model
{
    protected $table = 'thong_bao_da_doc';

    // Composite primary key (thong_bao_id, cu_dan_id) — no auto-increment
    public $incrementing = false;
    protected $primaryKey = null;

    // Only has read_at, no createdAt/updatedAt columns
    public $timestamps = false;

    protected $fillable = [
        'thong_bao_id', 'cu_dan_id', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function thongBao()
    {
        return $this->belongsTo(ThongBao::class, 'thong_bao_id');
    }

    public function cuDan()
    {
        return $this->belongsTo(CuDan::class, 'cu_dan_id');
    }
}
