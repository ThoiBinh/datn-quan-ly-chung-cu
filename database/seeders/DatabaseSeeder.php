<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Bảng phụ (không có foreign key)
            ChucVuSeeder::class,
            LoaiCanHoSeeder::class,
            LoaiPhiDichVuSeeder::class,
            DonViTinhPhiDichVuSeeder::class,
            LoaiTinhPhiDichVuSeeder::class,
            LoaiPhuongTienSeeder::class,
            TrangThaiCanHoSeeder::class,
            VaiTroSeeder::class,
            NguonTaoSeeder::class,
            ThuocTinhSeeder::class,

            // Bảng chính (theo thứ tự phụ thuộc)
            ToaNhaSeeder::class,
            NhanVienSeeder::class,
            LoaiYeuCauSeeder::class,
            CuDanSeeder::class,
            CanHoSeeder::class,
            CuDanCanHoSeeder::class,
            PhiDichVuSeeder::class,
            CanHoPhiDichVuSeeder::class,
            HoaDonSeeder::class,
            ChiTietHoaDonSeeder::class,
            PhuongTienSeeder::class,
            YeuCauCuDanSeeder::class,
            BangTinSeeder::class,
        ]);
    }
}
