<?php

namespace Database\Seeders;

use App\Models\ChucVu;
use App\Models\CuDan;
use App\Models\NhanVien;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminChucVuId = ChucVu::where('chuc_vu', 'Admin')->value('id');

        if ($adminChucVuId && !NhanVien::where('email', 'admin@chungcu.vn')->exists()) {
            NhanVien::create([
                'ho_ten'       => 'Admin Hệ Thống',
                'chuc_vu'      => $adminChucVuId,
                'email'        => 'admin@chungcu.vn',
                'mat_khau'     => Hash::make('12345678'),
                'trang_thai'   => 1,
                'sdt'          => '0900000001',
                'ma_nhan_vien' => 'AD001',
                'cccd'         => '001000000001',
            ]);
        }

        // Tạo tài khoản Manager nếu chưa có (dùng chuc_vu đầu tiên không phải Admin)
        if (!NhanVien::where('email', 'manager@chungcu.vn')->exists()) {
            $managerChucVuId = ChucVu::where('chuc_vu', '!=', 'Admin')->value('id') ?? 1;
            NhanVien::create([
                'ho_ten'       => 'Ban Quản Lý',
                'chuc_vu'      => $managerChucVuId,
                'email'        => 'manager@chungcu.vn',
                'mat_khau'     => Hash::make('12345678'),
                'trang_thai'   => 1,
                'sdt'          => '0900000002',
                'ma_nhan_vien' => 'MN001',
                'cccd'         => '001000000002',
            ]);
        }

        // Tạo tài khoản Cư Dân
        if (!CuDan::where('email', 'cudan1@chungcu.vn')->exists()) {
            CuDan::create([
                'ho_ten_dem' => 'Nguyễn Văn',
                'ten'        => 'An',
                'email'      => 'cudan1@chungcu.vn',
                'mat_khau'   => Hash::make('12345678'),
                'ngay_sinh'  => '1985-03-15',
                'cccd'       => '001085012345',
                'sdt'        => '0901234567',
                'trang_thai' => 1,
            ]);
        }

        if (!CuDan::where('email', 'cudan2@chungcu.vn')->exists()) {
            CuDan::create([
                'ho_ten_dem' => 'Trần Thị',
                'ten'        => 'Bình',
                'email'      => 'cudan2@chungcu.vn',
                'mat_khau'   => Hash::make('12345678'),
                'ngay_sinh'  => '1990-07-22',
                'cccd'       => '002090098765',
                'sdt'        => '0909876543',
                'trang_thai' => 1,
            ]);
        }
    }
}
