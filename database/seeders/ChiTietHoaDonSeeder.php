<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChiTietHoaDonSeeder extends Seeder
{
    public function run(): void
    {
        $hoaDons = DB::table('hoa_don')->get();

        foreach ($hoaDons as $hd) {
            $exists = DB::table('chi_tiet_hoa_don')->where('hoa_don', $hd->id)->exists();
            if ($exists) continue;

            // Chi tiết: phí quản lý + điện + nước
            $chiTiet = [
                [
                    'hoa_don'         => $hd->id,
                    'ten_phi_dich_vu' => 'Phí quản lý căn hộ',
                    'don_gia'         => 15000,
                    'chi_so_cu'       => null,
                    'chi_so_moi'      => null,
                    'so_luong'        => 50,
                    'thanh_tien'      => 750000,
                    'createdAt'       => now(),
                    'updatedAt'       => now(),
                ],
                [
                    'hoa_don'         => $hd->id,
                    'ten_phi_dich_vu' => 'Tiền điện',
                    'don_gia'         => 3500,
                    'chi_so_cu'       => rand(100, 300),
                    'chi_so_moi'      => null,
                    'so_luong'        => rand(100, 300),
                    'thanh_tien'      => rand(100, 300) * 3500,
                    'createdAt'       => now(),
                    'updatedAt'       => now(),
                ],
                [
                    'hoa_don'         => $hd->id,
                    'ten_phi_dich_vu' => 'Tiền nước',
                    'don_gia'         => 15000,
                    'chi_so_cu'       => rand(10, 30),
                    'chi_so_moi'      => null,
                    'so_luong'        => rand(5, 20),
                    'thanh_tien'      => rand(5, 20) * 15000,
                    'createdAt'       => now(),
                    'updatedAt'       => now(),
                ],
            ];

            // Cập nhật chi_so_moi = chi_so_cu + so_luong
            $chiTiet[1]['chi_so_moi'] = $chiTiet[1]['chi_so_cu'] + $chiTiet[1]['so_luong'];
            $chiTiet[2]['chi_so_moi'] = $chiTiet[2]['chi_so_cu'] + $chiTiet[2]['so_luong'];

            DB::table('chi_tiet_hoa_don')->insert($chiTiet);
        }
    }
}
