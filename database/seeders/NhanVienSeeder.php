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
            ['ho_ten' => 'Admin Hệ Thống',      'chuc_vu' => 6, 'sdt' => '0900000001', 'email' => 'admin@chungcu.vn',      'ma_nhan_vien' => 'AD001', 'cccd' => '001000000001', 'ngay_sinh' => '1980-01-01', 'ngay_vao_lam' => '2020-01-01'],
            ['ho_ten' => 'Nguyễn Quản Lý',      'chuc_vu' => 1, 'sdt' => '0901000001', 'email' => 'quanly@chungcu.vn',    'ma_nhan_vien' => 'NV001', 'cccd' => '079090000001', 'ngay_sinh' => '1985-03-12', 'ngay_vao_lam' => '2020-01-05'],
            ['ho_ten' => 'Trần Kế Toán',        'chuc_vu' => 2, 'sdt' => '0901000002', 'email' => 'ketoan@chungcu.vn',    'ma_nhan_vien' => 'NV002', 'cccd' => '079090000002', 'ngay_sinh' => '1990-07-21', 'ngay_vao_lam' => '2020-02-10'],
            ['ho_ten' => 'Lê Lễ Tân',           'chuc_vu' => 3, 'sdt' => '0901000003', 'email' => 'letan@chungcu.vn',     'ma_nhan_vien' => 'NV003', 'cccd' => '079090000003', 'ngay_sinh' => '1995-11-02', 'ngay_vao_lam' => '2021-03-15'],
            ['ho_ten' => 'Phạm Kỹ Thuật',       'chuc_vu' => 4, 'sdt' => '0901000004', 'email' => 'kythuat@chungcu.vn',   'ma_nhan_vien' => 'NV004', 'cccd' => '079090000004', 'ngay_sinh' => '1992-05-18', 'ngay_vao_lam' => '2021-06-01'],
            ['ho_ten' => 'Võ Bảo Vệ',           'chuc_vu' => 5, 'sdt' => '0901000005', 'email' => 'baove@chungcu.vn',     'ma_nhan_vien' => 'NV005', 'cccd' => '079090000005', 'ngay_sinh' => '1988-09-30', 'ngay_vao_lam' => '2022-01-20'],
            ['ho_ten' => 'Nguyễn Thị Hoa',      'chuc_vu' => 3, 'sdt' => '0901000007', 'email' => 'hoa.nt@chungcu.vn',   'ma_nhan_vien' => 'NV007', 'cccd' => '079090000007', 'ngay_sinh' => '1993-04-10', 'ngay_vao_lam' => '2022-03-01'],
            ['ho_ten' => 'Trần Văn Bình',       'chuc_vu' => 4, 'sdt' => '0901000008', 'email' => 'binh.tv@chungcu.vn',  'ma_nhan_vien' => 'NV008', 'cccd' => '079090000008', 'ngay_sinh' => '1991-08-25', 'ngay_vao_lam' => '2022-05-15'],
            ['ho_ten' => 'Lê Thị Mai',          'chuc_vu' => 2, 'sdt' => '0901000009', 'email' => 'mai.lt@chungcu.vn',   'ma_nhan_vien' => 'NV009', 'cccd' => '079090000009', 'ngay_sinh' => '1994-12-03', 'ngay_vao_lam' => '2023-01-10'],
            ['ho_ten' => 'Phạm Văn Dũng',       'chuc_vu' => 5, 'sdt' => '0901000010', 'email' => 'dung.pv@chungcu.vn',  'ma_nhan_vien' => 'NV010', 'cccd' => '079090000010', 'ngay_sinh' => '1987-06-17', 'ngay_vao_lam' => '2023-03-01'],
            ['ho_ten' => 'Hoàng Thị Lan',       'chuc_vu' => 3, 'sdt' => '0901000011', 'email' => 'lan.ht@chungcu.vn',   'ma_nhan_vien' => 'NV011', 'cccd' => '079090000011', 'ngay_sinh' => '1996-02-14', 'ngay_vao_lam' => '2023-04-01'],
            ['ho_ten' => 'Ngô Văn Hùng',        'chuc_vu' => 5, 'sdt' => '0901000012', 'email' => 'hung.nv@chungcu.vn',  'ma_nhan_vien' => 'NV012', 'cccd' => '079090000012', 'ngay_sinh' => '1986-07-22', 'ngay_vao_lam' => '2023-05-10'],
            ['ho_ten' => 'Đỗ Thị Bích',         'chuc_vu' => 2, 'sdt' => '0901000013', 'email' => 'bich.dt@chungcu.vn',  'ma_nhan_vien' => 'NV013', 'cccd' => '079090000013', 'ngay_sinh' => '1993-11-08', 'ngay_vao_lam' => '2023-06-15'],
            ['ho_ten' => 'Vũ Văn Tú',           'chuc_vu' => 4, 'sdt' => '0901000014', 'email' => 'tu.vv@chungcu.vn',    'ma_nhan_vien' => 'NV014', 'cccd' => '079090000014', 'ngay_sinh' => '1990-03-19', 'ngay_vao_lam' => '2023-07-01'],
            ['ho_ten' => 'Bùi Thị Thu',         'chuc_vu' => 3, 'sdt' => '0901000015', 'email' => 'thu.bt@chungcu.vn',   'ma_nhan_vien' => 'NV015', 'cccd' => '079090000015', 'ngay_sinh' => '1997-09-25', 'ngay_vao_lam' => '2023-08-20'],
            ['ho_ten' => 'Đinh Văn Khoa',       'chuc_vu' => 5, 'sdt' => '0901000016', 'email' => 'khoa.dv@chungcu.vn',  'ma_nhan_vien' => 'NV016', 'cccd' => '079090000016', 'ngay_sinh' => '1984-01-30', 'ngay_vao_lam' => '2023-09-01'],
            ['ho_ten' => 'Phan Thị Hằng',       'chuc_vu' => 3, 'sdt' => '0901000017', 'email' => 'hang.pt@chungcu.vn',  'ma_nhan_vien' => 'NV017', 'cccd' => '079090000017', 'ngay_sinh' => '1998-06-12', 'ngay_vao_lam' => '2023-10-15'],
            ['ho_ten' => 'Lý Văn Nam',          'chuc_vu' => 4, 'sdt' => '0901000018', 'email' => 'nam.lv@chungcu.vn',   'ma_nhan_vien' => 'NV018', 'cccd' => '079090000018', 'ngay_sinh' => '1989-04-07', 'ngay_vao_lam' => '2023-11-01'],
            ['ho_ten' => 'Trịnh Thị Ngọc',      'chuc_vu' => 2, 'sdt' => '0901000019', 'email' => 'ngoc.tt@chungcu.vn',  'ma_nhan_vien' => 'NV019', 'cccd' => '079090000019', 'ngay_sinh' => '1992-08-16', 'ngay_vao_lam' => '2023-12-01'],
            ['ho_ten' => 'Đặng Văn Thắng',      'chuc_vu' => 5, 'sdt' => '0901000020', 'email' => 'thang.dv@chungcu.vn', 'ma_nhan_vien' => 'NV020', 'cccd' => '079090000020', 'ngay_sinh' => '1983-12-28', 'ngay_vao_lam' => '2024-01-15'],
            ['ho_ten' => 'Nguyễn Văn Phú',      'chuc_vu' => 4, 'sdt' => '0901000021', 'email' => 'phu.nv@chungcu.vn',   'ma_nhan_vien' => 'NV021', 'cccd' => '079090000021', 'ngay_sinh' => '1991-02-09', 'ngay_vao_lam' => '2024-02-01'],
            ['ho_ten' => 'Lê Thị Tuyết',        'chuc_vu' => 3, 'sdt' => '0901000022', 'email' => 'tuyet.lt@chungcu.vn', 'ma_nhan_vien' => 'NV022', 'cccd' => '079090000022', 'ngay_sinh' => '1999-05-20', 'ngay_vao_lam' => '2024-02-15'],
            ['ho_ten' => 'Phạm Thị Hương',      'chuc_vu' => 2, 'sdt' => '0901000023', 'email' => 'huong.pt@chungcu.vn', 'ma_nhan_vien' => 'NV023', 'cccd' => '079090000023', 'ngay_sinh' => '1995-10-11', 'ngay_vao_lam' => '2024-03-01'],
            ['ho_ten' => 'Trần Văn Cường',      'chuc_vu' => 5, 'sdt' => '0901000024', 'email' => 'cuong.tv@chungcu.vn', 'ma_nhan_vien' => 'NV024', 'cccd' => '079090000024', 'ngay_sinh' => '1985-07-03', 'ngay_vao_lam' => '2024-03-15'],
            ['ho_ten' => 'Võ Thị Linh',         'chuc_vu' => 3, 'sdt' => '0901000025', 'email' => 'linh.vt@chungcu.vn',  'ma_nhan_vien' => 'NV025', 'cccd' => '079090000025', 'ngay_sinh' => '2000-01-15', 'ngay_vao_lam' => '2024-04-01'],
            ['ho_ten' => 'Hoàng Văn Đức',       'chuc_vu' => 4, 'sdt' => '0901000026', 'email' => 'duc.hv@chungcu.vn',   'ma_nhan_vien' => 'NV026', 'cccd' => '079090000026', 'ngay_sinh' => '1990-11-27', 'ngay_vao_lam' => '2024-04-15'],
            ['ho_ten' => 'Nguyễn Thị Thảo',     'chuc_vu' => 3, 'sdt' => '0901000027', 'email' => 'thao.nt@chungcu.vn',  'ma_nhan_vien' => 'NV027', 'cccd' => '079090000027', 'ngay_sinh' => '1998-03-22', 'ngay_vao_lam' => '2024-05-01'],
            ['ho_ten' => 'Lưu Văn Kiên',        'chuc_vu' => 5, 'sdt' => '0901000028', 'email' => 'kien.lv@chungcu.vn',  'ma_nhan_vien' => 'NV028', 'cccd' => '079090000028', 'ngay_sinh' => '1982-09-14', 'ngay_vao_lam' => '2024-05-15'],
            ['ho_ten' => 'Đỗ Văn Minh',         'chuc_vu' => 4, 'sdt' => '0901000029', 'email' => 'minh.dv@chungcu.vn',  'ma_nhan_vien' => 'NV029', 'cccd' => '079090000029', 'ngay_sinh' => '1993-06-30', 'ngay_vao_lam' => '2024-06-01'],
            ['ho_ten' => 'Bùi Văn Hải',         'chuc_vu' => 5, 'sdt' => '0901000030', 'email' => 'hai.bv@chungcu.vn',   'ma_nhan_vien' => 'NV030', 'cccd' => '079090000030', 'ngay_sinh' => '1986-04-18', 'ngay_vao_lam' => '2024-06-15'],
            ['ho_ten' => 'Phan Văn Long',        'chuc_vu' => 4, 'sdt' => '0901000031', 'email' => 'long.pv@chungcu.vn',  'ma_nhan_vien' => 'NV031', 'cccd' => '079090000031', 'ngay_sinh' => '1994-08-05', 'ngay_vao_lam' => '2024-07-01'],
            ['ho_ten' => 'Trần Thị Kim',        'chuc_vu' => 2, 'sdt' => '0901000032', 'email' => 'kim.tt@chungcu.vn',   'ma_nhan_vien' => 'NV032', 'cccd' => '079090000032', 'ngay_sinh' => '1997-12-10', 'ngay_vao_lam' => '2024-07-15'],
            ['ho_ten' => 'Vũ Thị Phương',       'chuc_vu' => 3, 'sdt' => '0901000033', 'email' => 'phuong.vt@chungcu.vn','ma_nhan_vien' => 'NV033', 'cccd' => '079090000033', 'ngay_sinh' => '2001-02-28', 'ngay_vao_lam' => '2024-08-01'],
            ['ho_ten' => 'Đinh Thị Loan',       'chuc_vu' => 3, 'sdt' => '0901000034', 'email' => 'loan.dt@chungcu.vn',  'ma_nhan_vien' => 'NV034', 'cccd' => '079090000034', 'ngay_sinh' => '1999-07-17', 'ngay_vao_lam' => '2024-08-15'],
            ['ho_ten' => 'Nguyễn Văn Toàn',     'chuc_vu' => 5, 'sdt' => '0901000035', 'email' => 'toan.nv@chungcu.vn',  'ma_nhan_vien' => 'NV035', 'cccd' => '079090000035', 'ngay_sinh' => '1981-10-23', 'ngay_vao_lam' => '2024-09-01'],
            ['ho_ten' => 'Lê Văn Sơn',          'chuc_vu' => 4, 'sdt' => '0901000036', 'email' => 'son.lv@chungcu.vn',   'ma_nhan_vien' => 'NV036', 'cccd' => '079090000036', 'ngay_sinh' => '1989-05-06', 'ngay_vao_lam' => '2024-09-15'],
            ['ho_ten' => 'Phạm Văn Tài',        'chuc_vu' => 4, 'sdt' => '0901000037', 'email' => 'tai.pv@chungcu.vn',   'ma_nhan_vien' => 'NV037', 'cccd' => '079090000037', 'ngay_sinh' => '1992-01-13', 'ngay_vao_lam' => '2024-10-01'],
            ['ho_ten' => 'Trương Thị Nga',      'chuc_vu' => 2, 'sdt' => '0901000038', 'email' => 'nga.tt@chungcu.vn',   'ma_nhan_vien' => 'NV038', 'cccd' => '079090000038', 'ngay_sinh' => '1996-09-01', 'ngay_vao_lam' => '2024-10-15'],
            ['ho_ten' => 'Võ Văn Khải',         'chuc_vu' => 5, 'sdt' => '0901000039', 'email' => 'khai.vv@chungcu.vn',  'ma_nhan_vien' => 'NV039', 'cccd' => '079090000039', 'ngay_sinh' => '1984-03-25', 'ngay_vao_lam' => '2024-11-01'],
            ['ho_ten' => 'Ngô Thị Diệu',        'chuc_vu' => 3, 'sdt' => '0901000040', 'email' => 'dieu.nt@chungcu.vn',  'ma_nhan_vien' => 'NV040', 'cccd' => '079090000040', 'ngay_sinh' => '2000-11-09', 'ngay_vao_lam' => '2024-11-15'],
            ['ho_ten' => 'Hoàng Văn Tùng',      'chuc_vu' => 5, 'sdt' => '0901000041', 'email' => 'tung.hv@chungcu.vn',  'ma_nhan_vien' => 'NV041', 'cccd' => '079090000041', 'ngay_sinh' => '1983-06-20', 'ngay_vao_lam' => '2024-12-01'],
            ['ho_ten' => 'Đặng Thị Xuân',       'chuc_vu' => 3, 'sdt' => '0901000042', 'email' => 'xuan.dt@chungcu.vn',  'ma_nhan_vien' => 'NV042', 'cccd' => '079090000042', 'ngay_sinh' => '1998-04-14', 'ngay_vao_lam' => '2024-12-15'],
            ['ho_ten' => 'Lý Thị Bảo',         'chuc_vu' => 2, 'sdt' => '0901000043', 'email' => 'bao.lt@chungcu.vn',   'ma_nhan_vien' => 'NV043', 'cccd' => '079090000043', 'ngay_sinh' => '1994-10-02', 'ngay_vao_lam' => '2025-01-06'],
            ['ho_ten' => 'Trịnh Văn Hậu',       'chuc_vu' => 4, 'sdt' => '0901000044', 'email' => 'hau.tv@chungcu.vn',   'ma_nhan_vien' => 'NV044', 'cccd' => '079090000044', 'ngay_sinh' => '1990-07-29', 'ngay_vao_lam' => '2025-01-20'],
            ['ho_ten' => 'Phan Văn Hưng',       'chuc_vu' => 5, 'sdt' => '0901000045', 'email' => 'hung.pv@chungcu.vn',  'ma_nhan_vien' => 'NV045', 'cccd' => '079090000045', 'ngay_sinh' => '1985-02-11', 'ngay_vao_lam' => '2025-02-03'],
            ['ho_ten' => 'Nguyễn Thị Cẩm',     'chuc_vu' => 3, 'sdt' => '0901000046', 'email' => 'cam.nt@chungcu.vn',   'ma_nhan_vien' => 'NV046', 'cccd' => '079090000046', 'ngay_sinh' => '2001-08-06', 'ngay_vao_lam' => '2025-02-17'],
            ['ho_ten' => 'Lê Văn Đạt',          'chuc_vu' => 4, 'sdt' => '0901000047', 'email' => 'dat.lv@chungcu.vn',   'ma_nhan_vien' => 'NV047', 'cccd' => '079090000047', 'ngay_sinh' => '1993-05-24', 'ngay_vao_lam' => '2025-03-03'],
            ['ho_ten' => 'Phạm Thị Quỳnh',      'chuc_vu' => 2, 'sdt' => '0901000048', 'email' => 'quynh.pt@chungcu.vn', 'ma_nhan_vien' => 'NV048', 'cccd' => '079090000048', 'ngay_sinh' => '1997-01-31', 'ngay_vao_lam' => '2025-03-17'],
            ['ho_ten' => 'Bùi Văn Thịnh',       'chuc_vu' => 5, 'sdt' => '0901000049', 'email' => 'thinh.bv@chungcu.vn', 'ma_nhan_vien' => 'NV049', 'cccd' => '079090000049', 'ngay_sinh' => '1987-11-15', 'ngay_vao_lam' => '2025-04-01'],
            ['ho_ten' => 'Đỗ Thị Phúc',         'chuc_vu' => 1, 'sdt' => '0901000050', 'email' => 'phuc.dt@chungcu.vn',  'ma_nhan_vien' => 'NV050', 'cccd' => '079090000050', 'ngay_sinh' => '1988-08-08', 'ngay_vao_lam' => '2025-04-15'],
        ];

        foreach ($data as $item) {
            if (!DB::table('nhan_vien')->where('email', $item['email'])->exists()) {
                DB::table('nhan_vien')->insert(array_merge($item, [
                    'mat_khau'  => $password,
                    'trang_thai' => 1,
                    'createdAt' => now(),
                    'updatedAt' => now(),
                ]));
            }
        }
    }
}
