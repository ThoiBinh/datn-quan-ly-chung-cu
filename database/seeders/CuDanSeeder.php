<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CuDanSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('12345678');

        $hoTenDem = ['Nguyễn Văn', 'Trần Thị', 'Lê Văn', 'Phạm Thị', 'Võ Văn', 'Đặng Thị', 'Bùi Văn', 'Hoàng Thị', 'Vũ Văn', 'Đỗ Thị'];
        $ten = ['An', 'Bình', 'Cường', 'Dung', 'Em', 'Phương', 'Giang', 'Hoa', 'Khánh', 'Lan', 'Minh', 'Nam', 'Oanh', 'Phúc', 'Quân', 'Sơn', 'Thảo', 'Uyên', 'Việt', 'Xuân', 'Yến', 'Anh', 'Châu', 'Duyên', 'Hải'];
        $tinh = ['TP. Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng', 'Bình Dương', 'Đồng Nai'];

        $data = [];
        for ($i = 1; $i <= 50; $i++) {
            $htd = $hoTenDem[($i - 1) % count($hoTenDem)];
            $t   = $ten[($i - 1) % count($ten)];
            $data[] = [
                'ho_ten_dem'     => $htd,
                'ten'            => $t,
                'sdt'            => '090' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'cccd'           => '07909' . str_pad($i, 9, '0', STR_PAD_LEFT),
                'email'          => 'cudan' . $i . '@chungcu.vn',
                'mat_khau'       => $password,
                'ngay_sinh'      => date('Y-m-d', mktime(0, 0, 0, rand(1, 12), rand(1, 28), rand(1970, 2000))),
                'gioi_tinh'      => $i % 2,
                'tinh'           => $tinh[$i % count($tinh)],
                'xa'             => 'Phường ' . rand(1, 15),
                'dia_chi'        => rand(1, 999) . ' Đường số ' . rand(1, 50),
                'trang_thai'     => 1,
                'nguoi_cap_nhat' => 1,
                'createdAt'      => now(),
                'updatedAt'      => now(),
            ];
        }

        foreach ($data as $item) {
            if (!DB::table('cu_dan')->where('email', $item['email'])->exists()) {
                DB::table('cu_dan')->insert($item);
            }
        }
    }
}
