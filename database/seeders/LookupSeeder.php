<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('loai_can_ho')->exists()) {
            return;
        }

        DB::table('loai_can_ho')->insert([
            ['ten_loai_can_ho' => 'Studio'],
            ['ten_loai_can_ho' => '1 Phòng ngủ'],
            ['ten_loai_can_ho' => '2 Phòng ngủ'],
            ['ten_loai_can_ho' => '3 Phòng ngủ'],
            ['ten_loai_can_ho' => 'Penthouse'],
        ]);

        DB::table('trang_thai_can_ho')->insert([
            ['ten_trang_thai' => 'Trống'],
            ['ten_trang_thai' => 'Đang sử dụng'],
            ['ten_trang_thai' => 'Đang bảo trì'],
            ['ten_trang_thai' => 'Chờ bàn giao'],
        ]);

        DB::table('loai_hop_dong')->insert([
            ['ten_loai' => 'Hợp đồng mua bán'],
            ['ten_loai' => 'Hợp đồng thuê'],
        ]);

        DB::table('loai_phi_dich_vu')->insert([
            ['ten_loai_phi_dich_vu' => 'Phí quản lý'],
            ['ten_loai_phi_dich_vu' => 'Phí gửi xe'],
            ['ten_loai_phi_dich_vu' => 'Phí điện'],
            ['ten_loai_phi_dich_vu' => 'Phí nước'],
            ['ten_loai_phi_dich_vu' => 'Internet'],
        ]);

        DB::table('don_vi_tinh_phi_dich_vu')->insert([
            ['don_vi' => 'Tháng'],
            ['don_vi' => 'Căn hộ/Tháng'],
            ['don_vi' => 'Xe/Tháng'],
            ['don_vi' => 'kWh'],
            ['don_vi' => 'm³'],
        ]);

        DB::table('loai_tinh_phi_dich_vu')->insert([
            ['ten_loai' => 'Cố định'],
            ['ten_loai' => 'Theo chỉ số'],
            ['ten_loai' => 'Theo diện tích'],
        ]);

        DB::table('loai_phuong_tien')->insert([
            ['ten_loai_phuong_tien' => 'Xe máy'],
            ['ten_loai_phuong_tien' => 'Ô tô'],
            ['ten_loai_phuong_tien' => 'Xe đạp điện'],
            ['ten_loai_phuong_tien' => 'Xe đạp'],
        ]);

        DB::table('vai_tro')->insert([
            ['vai_tro' => 'Chủ hộ'],
            ['vai_tro' => 'Thành viên'],
            ['vai_tro' => 'Người thuê'],
        ]);

        DB::table('nguon_tao')->insert([
            ['ten_nguon_tao' => 'Admin'],
            ['ten_nguon_tao' => 'Cư dân (App)'],
            ['ten_nguon_tao' => 'MoMo'],
            ['ten_nguon_tao' => 'VNPay'],
        ]);

        DB::table('chuc_vu')->insert([
            ['chuc_vu' => 'Trưởng ban quản lý'],
            ['chuc_vu' => 'Nhân viên quản lý'],
            ['chuc_vu' => 'Kế toán'],
            ['chuc_vu' => 'Bảo vệ'],
            ['chuc_vu' => 'Kỹ thuật'],
        ]);

        DB::table('thuoc_tinh')->insert([
            ['ten_thuoc_tinh' => 'Diện tích'],
            ['ten_thuoc_tinh' => 'Số phòng ngủ'],
            ['ten_thuoc_tinh' => 'Số phòng tắm'],
            ['ten_thuoc_tinh' => 'Hướng ban công'],
            ['ten_thuoc_tinh' => 'Tầng'],
        ]);

        DB::table('cau_hinh_thanh_toan')->insert([
            [
                'loai_phuong_thuc' => 'MOMO',
                'ten_nha_cung_cap' => 'MoMo',
                'trang_thai' => 1,
            ],
            [
                'loai_phuong_thuc' => 'VNPAY',
                'ten_nha_cung_cap' => 'VNPay QR',
                'trang_thai' => 1,
            ],
        ]);
    }
}
