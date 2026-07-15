<?php

use App\Models\NhanVien;
use Illuminate\Support\Facades\Broadcast;

// Kênh public: chỉ phát số liệu sức chứa/hàng chờ (không dữ liệu cá nhân) của MỘT tiện ích,
// dùng để bật/tắt nút "Đặt lịch" real-time trên trang tạo lịch — không cần xác thực.
// (Không cần khai báo ở đây — kênh public không đi qua Broadcast::channel().)

// Kênh private cho Admin/Manager: danh sách/dashboard đặt lịch tiện ích, xác thực qua guard
// 'nhanvien' (không dùng guard 'web' mặc định — dự án không có guard này).
Broadcast::channel('dat-lich-tien-ich.nhanvien', function (NhanVien $nhanVien) {
    return (int) $nhanVien->trang_thai === 1;
}, ['guards' => ['nhanvien']]);

// Kênh private cho từng cư dân: chỉ nhận cập nhật về đúng lượt đặt lịch của chính mình,
// xác thực qua guard 'cudan'.
Broadcast::channel('dat-lich-tien-ich.cudan.{cuDanId}', function ($cuDan, int $cuDanId) {
    return (int) $cuDan->id === $cuDanId;
}, ['guards' => ['cudan']]);
