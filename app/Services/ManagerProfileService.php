<?php

namespace App\Services;

use App\Models\NhanVien;
use Illuminate\Support\Facades\Hash;

class ManagerProfileService
{
    /**
     * Chỉ cập nhật sdt — mọi khóa khác lỡ có trong $data (email, chuc_vu, cccd, ...)
     * đều bị bỏ qua để Manager không thể tự nâng quyền/đổi thông tin bị khóa dù
     * FormRequest có bị bypass.
     */
    public function updateProfile(NhanVien $nhanVien, array $data): NhanVien
    {
        $old = $nhanVien->only(['sdt']);

        $nhanVien->update(['sdt' => $data['sdt']]);

        AuditLogService::log('UPDATE', 'nhan_vien', $nhanVien->id, $old, $nhanVien->only(['sdt']));

        return $nhanVien;
    }

    public function changePassword(NhanVien $nhanVien, string $newPassword): NhanVien
    {
        $nhanVien->update(['mat_khau' => Hash::make($newPassword)]);

        AuditLogService::log('UPDATE', 'nhan_vien', $nhanVien->id, null, ['ly_do' => 'Tự đổi mật khẩu']);

        return $nhanVien;
    }
}
