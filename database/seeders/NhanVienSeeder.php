<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NhanVienSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('12345678');

        $data = [
            ['ho_ten' => 'Admin Hệ Thống',   'chuc_vu' => 6, 'sdt' => '0900000001', 'email' => 'admin@chungcu.vn',    'mat_khau' => $password, 'trang_thai' => 1, 'ma_nhan_vien' => 'AD001', 'cccd' => '001000000001', 'ngay_sinh' => '1980-01-01', 'ngay_vao_lam' => '2020-01-01'],
            ['ho_ten' => 'Nguyễn Quản Lý',   'chuc_vu' => 1, 'sdt' => '0901000001', 'email' => 'quanly@chungcu.vn',  'mat_khau' => $password, 'trang_thai' => 1, 'ma_nhan_vien' => 'NV001', 'cccd' => '079090000001', 'ngay_sinh' => '1985-03-12', 'ngay_vao_lam' => '2020-01-05'],
            ['ho_ten' => 'Trần Kế Toán',     'chuc_vu' => 2, 'sdt' => '0901000002', 'email' => 'ketoan@chungcu.vn',  'mat_khau' => $password, 'trang_thai' => 1, 'ma_nhan_vien' => 'NV002', 'cccd' => '079090000002', 'ngay_sinh' => '1990-07-21', 'ngay_vao_lam' => '2020-02-10'],
            ['ho_ten' => 'Lê Lễ Tân',        'chuc_vu' => 3, 'sdt' => '0901000003', 'email' => 'letan@chungcu.vn',   'mat_khau' => $password, 'trang_thai' => 1, 'ma_nhan_vien' => 'NV003', 'cccd' => '079090000003', 'ngay_sinh' => '1995-11-02', 'ngay_vao_lam' => '2021-03-15'],
            ['ho_ten' => 'Phạm Kỹ Thuật',    'chuc_vu' => 4, 'sdt' => '0901000004', 'email' => 'kythuat@chungcu.vn', 'mat_khau' => $password, 'trang_thai' => 1, 'ma_nhan_vien' => 'NV004', 'cccd' => '079090000004', 'ngay_sinh' => '1992-05-18', 'ngay_vao_lam' => '2021-06-01'],
            ['ho_ten' => 'Võ Bảo Vệ',        'chuc_vu' => 5, 'sdt' => '0901000005', 'email' => 'baove@chungcu.vn',   'mat_khau' => $password, 'trang_thai' => 1, 'ma_nhan_vien' => 'NV005', 'cccd' => '079090000005', 'ngay_sinh' => '1988-09-30', 'ngay_vao_lam' => '2022-01-20'],
            ['ho_ten' => 'Nguyễn Thị Hoa',   'chuc_vu' => 3, 'sdt' => '0901000007', 'email' => 'hoa.nt@chungcu.vn', 'mat_khau' => $password, 'trang_thai' => 1, 'ma_nhan_vien' => 'NV007', 'cccd' => '079090000007', 'ngay_sinh' => '1993-04-10', 'ngay_vao_lam' => '2022-03-01'],
            ['ho_ten' => 'Trần Văn Bình',    'chuc_vu' => 4, 'sdt' => '0901000008', 'email' => 'binh.tv@chungcu.vn', 'mat_khau' => $password, 'trang_thai' => 1, 'ma_nhan_vien' => 'NV008', 'cccd' => '079090000008', 'ngay_sinh' => '1991-08-25', 'ngay_vao_lam' => '2022-05-15'],
            ['ho_ten' => 'Lê Thị Mai',       'chuc_vu' => 2, 'sdt' => '0901000009', 'email' => 'mai.lt@chungcu.vn',  'mat_khau' => $password, 'trang_thai' => 1, 'ma_nhan_vien' => 'NV009', 'cccd' => '079090000009', 'ngay_sinh' => '1994-12-03', 'ngay_vao_lam' => '2023-01-10'],
            ['ho_ten' => 'Phạm Văn Dũng',    'chuc_vu' => 5, 'sdt' => '0901000010', 'email' => 'dung.pv@chungcu.vn', 'mat_khau' => $password, 'trang_thai' => 1, 'ma_nhan_vien' => 'NV010', 'cccd' => '079090000010', 'ngay_sinh' => '1987-06-17', 'ngay_vao_lam' => '2023-03-01'],
        ];

        foreach ($data as $item) {
            if (!DB::table('nhan_vien')->where('email', $item['email'])->exists()) {
                DB::table('nhan_vien')->insert(array_merge($item, [
                    'createdAt' => now(),
                    'updatedAt' => now(),
                ]));
            }
        }
    }
}
