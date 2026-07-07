<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class BangTin extends Model
{
    use SoftDeletes;

    protected $table = 'bang_tin';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $fillable = [
        'tieu_de', 'noi_dung', 'hinh_url', 'nguoi_tao', 'nguoi_cap_nhat',
    ];

    public function getCreatedAtAttribute(): ?Carbon
    {
        return isset($this->attributes['createdAt']) && $this->attributes['createdAt']
            ? Carbon::parse($this->attributes['createdAt'])
            : null;
    }

    /**
     * URL đầy đủ của hình bảng tin (qua Storage), null nếu chưa có ảnh hoặc file không tồn tại.
     */
    public function getHinhUrlFullAttribute(): ?string
    {
        if (!$this->hinh_url || !Storage::disk('public')->exists($this->hinh_url)) {
            return null;
        }

        return Storage::disk('public')->url($this->hinh_url);
    }

    public function nguoiTao()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_tao')->withTrashed();
    }

    public function nguoiCapNhat()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_cap_nhat')->withTrashed();
    }
}
