<?php

namespace App\Services;

use App\Models\NhanVien;
use Illuminate\Support\Carbon;

class NhanVienTrangThaiService
{
    /**
     * Đồng bộ hàng loạt: mọi nhân viên có ngay_nghi_lam nhỏ hơn hôm nay
     * và vẫn đang ở trạng thái "đang làm việc" thì chuyển sang "đã nghỉ".
     * Dùng trước khi liệt kê danh sách / tính thống kê.
     */
    public static function syncExpired(): void
    {
        NhanVien::whereNotNull('ngay_nghi_lam')
            ->where('ngay_nghi_lam', '<', Carbon::today())
            ->where('trang_thai', NhanVien::TRANG_THAI_DANG_LAM)
            ->update(['trang_thai' => NhanVien::TRANG_THAI_DA_NGHI]);
    }

    /**
     * Đồng bộ trạng thái cho một nhân viên cụ thể (dùng khi xem chi tiết / sửa)
     * và trả về bản ghi với trạng thái mới nhất.
     */
    public static function syncOne(NhanVien $nhanVien): NhanVien
    {
        if (
            $nhanVien->ngay_nghi_lam
            && $nhanVien->ngay_nghi_lam->lt(Carbon::today())
            && $nhanVien->trang_thai == NhanVien::TRANG_THAI_DANG_LAM
        ) {
            $nhanVien->trang_thai = NhanVien::TRANG_THAI_DA_NGHI;
            $nhanVien->save();
        }

        return $nhanVien;
    }
}
