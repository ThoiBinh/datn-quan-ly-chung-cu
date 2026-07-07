<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YeuCauCuDanSeeder extends Seeder
{
    public function run(): void
    {
        // Dữ liệu demo được sinh ngẫu nhiên (ngay_gui không xác định), không có khóa tự
        // nhiên để đối chiếu idempotent -> bỏ qua nếu bảng đã có dữ liệu.
        if (DB::table('yeu_cau_cu_dan')->exists()) {
            return;
        }

        // loai_yeu_cau: 1=Sửa chữa, 2=Khiếu nại, 3=Hỏi đáp, 4=Đăng ký phương tiện
        // muc_do_uu_tien: 1=Thấp, 2=Trung bình, 3=Cao
        // trang_thai: 1=Chờ xử lý, 2=Đang xử lý, 3=Hoàn thành, 4=Từ chối
        $cuDanIds   = DB::table('cu_dan')->pluck('id')->toArray();
        $nhanVienId = DB::table('nhan_vien')->where('chuc_vu', '!=', 6)->value('id');

        $tieude = [
            'Sửa chữa' => ['Vòi nước bị rỉ', 'Điện trong phòng bị mất', 'Thang máy bị hỏng', 'Cửa sổ bị vỡ kính', 'Điều hòa không hoạt động'],
            'Khiếu nại' => ['Khiếu nại về tiếng ồn', 'Phí quản lý tăng bất hợp lý', 'Vệ sinh hành lang kém', 'Bãi giữ xe lộn xộn', 'Hàng xóm xả rác'],
            'Hỏi đáp' => ['Hỏi về lịch đóng phí', 'Thủ tục đăng ký xe', 'Quy định về nuôi thú cưng', 'Thủ tục chuyển nhượng', 'Hỏi về phòng sinh hoạt cộng đồng'],
            'Đăng ký phương tiện' => ['Đăng ký xe ô tô mới', 'Đăng ký thêm xe máy', 'Hủy đăng ký xe cũ', 'Đổi thông tin xe', 'Đăng ký xe đạp điện'],
        ];

        $loaiMap = [1 => 'Sửa chữa', 2 => 'Khiếu nại', 3 => 'Hỏi đáp', 4 => 'Đăng ký phương tiện'];

        $data = [];
        for ($i = 1; $i <= 50; $i++) {
            $cuDanId  = $cuDanIds[($i - 1) % count($cuDanIds)];
            $loai     = ($i % 4) + 1;
            $tenLoai  = $loaiMap[$loai];
            $trangThai = ($i % 4) + 1;
            $tieuDeList = $tieude[$tenLoai];
            $tieude_item = $tieuDeList[($i - 1) % count($tieuDeList)];

            $data[] = [
                'cu_dan'           => $cuDanId,
                'loai_yeu_cau'     => $loai,
                'tieu_de'          => $tieude_item,
                'noi_dung'         => 'Kính gửi ban quản lý, ' . strtolower($tieude_item) . '. Mong ban quản lý xem xét và giải quyết sớm. Xin cảm ơn.',
                'ngay_gui'         => date('Y-m-d H:i:s', mktime(0, 0, 0, rand(1, 6), rand(1, 28), 2025)),
                'muc_do_uu_tien'   => ($i % 3) + 1,
                'trang_thai'       => $trangThai > 4 ? 1 : $trangThai,
                'nhan_vien_xu_ly'  => $trangThai > 1 ? $nhanVienId : null,
                'ngay_hoan_thanh'  => $trangThai == 3 ? now() : null,
                'nguoi_cap_nhat'   => $nhanVienId,
                'createdAt'        => now(),
                'updatedAt'        => now(),
            ];
        }

        if (DB::table('yeu_cau_cu_dan')->count() === 0) {
            DB::table('yeu_cau_cu_dan')->insert($data);
        }
    }
}
