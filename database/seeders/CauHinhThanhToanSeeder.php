<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CauHinhThanhToanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'loai_phuong_thuc'      => 'bank_transfer',
                'ten_nha_cung_cap'      => 'Ngân hàng Vietcombank',
                'dinh_danh_thu_huong'   => '1234567890',
                'ma_nhan_dien'          => 'VCB',
                'ten_chu_tai_khoan'     => 'CONG TY QUAN LY CHUNG CU ABC',
                'trang_thai'            => 1,
                'nguoi_cap_nhat'        => 1,
                'createdAt'             => now(),
                'updatedAt'             => now(),
            ],
            [
                'loai_phuong_thuc'      => 'momo',
                'ten_nha_cung_cap'      => 'MoMo',
                'dinh_danh_thu_huong'   => '0909123456',
                'ma_nhan_dien'          => 'MOMO',
                'ten_chu_tai_khoan'     => 'Chung cư ABC',
                'trang_thai'            => 0,
                'nguoi_cap_nhat'        => 1,
                'createdAt'             => now(),
                'updatedAt'             => now(),
            ],
            [
                'loai_phuong_thuc'      => 'vnpay',
                'ten_nha_cung_cap'      => 'VNPay',
                'dinh_danh_thu_huong'   => null,
                'ma_nhan_dien'          => 'VNPAY',
                'ten_chu_tai_khoan'     => null,
                'trang_thai'            => 0,
                'nguoi_cap_nhat'        => 1,
                'createdAt'             => now(),
                'updatedAt'             => now(),
            ],
        ];

        foreach ($data as $item) {
            $exists = DB::table('cau_hinh_thanh_toan')
                ->where('loai_phuong_thuc', $item['loai_phuong_thuc'])
                ->exists();
            if (!$exists) {
                DB::table('cau_hinh_thanh_toan')->insert($item);
            }
        }
    }
}
