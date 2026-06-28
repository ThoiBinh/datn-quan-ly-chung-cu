<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HoaDonSeeder extends Seeder
{
    public function run(): void
    {
        $canHoIds = DB::table('can_ho')->where('trang_thai', 2)->pluck('id')->take(50)->toArray();

        $data = [];
        foreach ($canHoIds as $index => $canHoId) {
            $thang = ($index % 6) + 1;
            $nam   = 2025;
            $tong  = rand(1500000, 5000000);
            $daTT  = ($index % 3 === 0) ? $tong : 0;
            // trang_thai: 1=chưa thanh toán, 2=đã thanh toán, 3=quá hạn
            $trangThai = $daTT >= $tong ? 2 : ($thang < date('n') ? 3 : 1);

            $data[] = [
                'ma_thanh_toan'          => 'HD' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'can_ho'                 => $canHoId,
                'thang'                  => $thang,
                'nam'                    => $nam,
                'tong_tien'              => $tong,
                'so_tien_da_thanh_toan'  => $daTT,
                'chi_phi'                => 0,
                'han_thanh_toan'         => date('Y-m-d', mktime(0, 0, 0, $thang + 1, 15, $nam)),
                'trang_thai'             => $trangThai,
                'nguoi_cap_nhat'         => 1,
                'createdAt'              => now(),
                'updatedAt'              => now(),
            ];
        }

        foreach ($data as $item) {
            $exists = DB::table('hoa_don')
                ->where('can_ho', $item['can_ho'])
                ->where('thang', $item['thang'])
                ->where('nam', $item['nam'])
                ->exists();
            if (!$exists) {
                DB::table('hoa_don')->insert($item);
            }
        }
    }
}
