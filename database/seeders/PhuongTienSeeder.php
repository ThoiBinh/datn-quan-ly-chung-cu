<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhuongTienSeeder extends Seeder
{
    public function run(): void
    {
        // bien_so được sinh ngẫu nhiên nên không thể dùng làm khóa idempotent giữa
        // các lần chạy khác nhau -> bỏ qua nếu bảng đã có dữ liệu.
        if (DB::table('phuong_tien')->exists()) {
            return;
        }

        // loai_phuong_tien: 1=Ô tô, 2=Xe máy, 3=Xe đạp điện, 4=Xe đạp
        $canHoIds = DB::table('can_ho')->where('trang_thai', 2)->pluck('id')->toArray();
        $prefix   = ['51', '59', '43', '30', '29'];

        $data = [];
        for ($i = 1; $i <= 50; $i++) {
            $canHoId = $canHoIds[($i - 1) % count($canHoIds)];
            $loai    = ($i % 4) + 1 > 4 ? 2 : ($i % 4) + 1;
            $p       = $prefix[$i % count($prefix)];

            $data[] = [
                'ten_phuong_tien' => $loai == 1 ? 'Toyota Vios' : ($loai == 2 ? 'Honda Wave' : 'VinFast Klara'),
                'bien_so'         => $p . '-' . rand(100, 999) . '.' . rand(10, 99),
                'loai_phuong_tien'=> $loai > 4 ? 2 : $loai,
                'can_ho'          => $canHoId,
                'ngay_dang_ky'    => date('Y-m-d', mktime(0, 0, 0, rand(1, 12), rand(1, 28), rand(2021, 2024))),
                'trang_thai'      => 1,
                'nguoi_cap_nhat'  => 1,
            ];
        }

        foreach ($data as $item) {
            $exists = DB::table('phuong_tien')->where('bien_so', $item['bien_so'])->exists();
            if (!$exists) {
                DB::table('phuong_tien')->insert($item);
            }
        }
    }
}
