<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoaiTienIch extends Model
{
    use SoftDeletes;

    protected $table = 'loai_tien_ich';
    public $timestamps = false;

    const DELETED_AT = 'deletedAt';

    protected $fillable = ['ten_loai_tien_ich'];

    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Chuẩn hoá tên loại tiện ích khi gán giá trị (tránh khoảng trắng thừa).
     */
    public function setTenLoaiTienIchAttribute(?string $value): void
    {
        $this->attributes['ten_loai_tien_ich'] = $value !== null ? trim($value) : $value;
    }

    public function tienIch()
    {
        return $this->hasMany(TienIch::class, 'loai_tien_ich');
    }

    /**
     * Các lượt đặt lịch của tất cả tiện ích thuộc loại này (qua bảng trung gian tien_ich).
     */
    public function datLich(): HasManyThrough
    {
        return $this->hasManyThrough(DatLichTienIch::class, TienIch::class, 'loai_tien_ich', 'tien_ich');
    }

    /**
     * Chỉ lấy các tiện ích đang hoạt động thuộc loại này.
     */
    public function tienIchHoatDong()
    {
        return $this->hasMany(TienIch::class, 'loai_tien_ich')
            ->where('trang_thai', TienIch::TRANG_THAI_HOAT_DONG);
    }

    /**
     * Không có cột trạng thái trên bảng loai_tien_ich, nên "active" được hiểu
     * là bản ghi chưa bị xoá mềm (mặc định của Eloquent đã tự loại trừ deletedAt).
     * Scope này giữ để đồng bộ interface với TienIch/DatLichTienIch.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('deletedAt');
    }
}
