<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class YeuCauCuDan extends Model
{
    protected $table = 'yeu_cau_cu_dan';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'cu_dan', 'loai_yeu_cau', 'tieu_de', 'noi_dung', 'ngay_gui', 'muc_do_uu_tien',
        'trang_thai', 'nhan_vien_xu_ly', 'ngay_hoan_thanh', 'nguoi_cap_nhat',
    ];

    protected $casts = [
        'ngay_gui'        => 'datetime',
        'ngay_hoan_thanh' => 'datetime',
    ];

    const MUC_DO_THAP = 1;
    const MUC_DO_TRUNG_BINH = 2;
    const MUC_DO_KHAN_CAP = 3;

    const TRANG_THAI_MOI = 1;
    const TRANG_THAI_DANG_XU_LY = 2;
    const TRANG_THAI_HOAN_THANH = 3;
    const TRANG_THAI_TU_CHOI = 4;

    // Accessor để view dùng $model->created_at hoạt động với column createdAt
    public function getCreatedAtAttribute(): ?Carbon
    {
        return isset($this->attributes['createdAt']) && $this->attributes['createdAt']
            ? Carbon::parse($this->attributes['createdAt'])
            : null;
    }

    public function cuDan()
    {
        return $this->belongsTo(CuDan::class, 'cu_dan');
    }

    public function nhanVienXuLy()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_xu_ly');
    }

    public function loaiYeuCau()
    {
        return $this->belongsTo(LoaiYeuCau::class, 'loai_yeu_cau');
    }

    public function getMucDoLabelAttribute(): array
    {
        return match((int)$this->muc_do_uu_tien) {
            1 => ['text' => 'Thấp', 'class' => 'bg-gray-100 text-gray-600'],
            2 => ['text' => 'Trung bình', 'class' => 'bg-blue-100 text-blue-700'],
            3 => ['text' => 'Cao', 'class' => 'bg-orange-100 text-orange-700'],
            4 => ['text' => 'Khẩn cấp', 'class' => 'bg-red-100 text-red-700'],
            default => ['text' => 'Không xác định', 'class' => 'bg-gray-100 text-gray-500'],
        };
    }

    public function getTrangThaiLabelAttribute(): array
    {
        return match((int)$this->trang_thai) {
            1 => ['text' => 'Mới', 'class' => 'bg-blue-100 text-blue-700'],
            2 => ['text' => 'Đang xử lý', 'class' => 'bg-yellow-100 text-yellow-700'],
            3 => ['text' => 'Hoàn thành', 'class' => 'bg-green-100 text-green-700'],
            4 => ['text' => 'Từ chối', 'class' => 'bg-red-100 text-red-700'],
            default => ['text' => 'Không xác định', 'class' => 'bg-gray-100 text-gray-500'],
        };
    }
}
