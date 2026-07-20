<?php

namespace App\Services;

use App\Models\NhatKyHeThong;

class AuditLogService
{
    public static function log(
        string $hanhDong,
        string $bangTacDong,
        int $idBanGhi,
        mixed $giaTriCu = null,
        mixed $giaTriMoi = null
    ): void {
        NhatKyHeThong::create([
            // nguoi_thuc_hien có FK trỏ về bảng nhan_vien (xem NhatKyHeThong::nguoiThucHien()),
            // nên không được gán id của cư dân vào đây — sẽ vi phạm khóa ngoại. Khi hành động
            // do chính cư dân tự thực hiện (không qua nhân viên), giá trị này để null.
            'nguoi_thuc_hien' => auth('nhanvien')->id(),
            'thoi_gian'       => now(),
            'hanh_dong'       => $hanhDong,
            'bang_tac_dong'   => $bangTacDong,
            'id_ban_ghi'      => $idBanGhi,
            'gia_tri_cu'      => $giaTriCu ? json_encode($giaTriCu, JSON_UNESCAPED_UNICODE) : null,
            'gia_tri_moi'     => $giaTriMoi ? json_encode($giaTriMoi, JSON_UNESCAPED_UNICODE) : null,
            'createdAt'       => now(),
        ]);
    }
}
