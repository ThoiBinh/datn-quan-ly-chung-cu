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
            'nguoi_thuc_hien' => auth('nhanvien')->id() ?? auth('cudan')->id(),
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
