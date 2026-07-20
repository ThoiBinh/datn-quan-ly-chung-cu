<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CuDanCanHoSeeder extends Seeder
{
    public function run(): void
    {
        // Gán 50 cư dân vào 40 căn hộ đang ở (trang_thai = 2)
        // vai_tro: 1=Chủ hộ, 2=Thành viên
        $canHoDangO = DB::table('can_ho')->where('trang_thai', 2)->pluck('id')->toArray();
        $cuDanIds   = DB::table('cu_dan')->pluck('id')->toArray();

        if (empty($canHoDangO) || empty($cuDanIds)) return;

        $data = [];
        $cuDanIndex = 0;

        foreach ($canHoDangO as $canHoId) {
            if ($cuDanIndex >= count($cuDanIds)) break;

            // Mỗi căn hộ có 1 chủ hộ
            $data[] = [
                'cu_dan'          => $cuDanIds[$cuDanIndex],
                'can_ho'          => $canHoId,
                'vai_tro'         => 1,
                'ngay_chuyen_den' => '2023-01-01 00:00:00',
                'trang_thai'      => 1,
                'nguoi_cap_nhat'  => 1,
                'createdAt'       => now(),
                'updatedAt'       => now(),
            ];
            $cuDanIndex++;

            // 10 căn hộ đầu có thêm 1 thành viên
            if ($cuDanIndex < count($cuDanIds) && $canHoId <= $canHoDangO[9]) {
                $data[] = [
                    'cu_dan'          => $cuDanIds[$cuDanIndex],
                    'can_ho'          => $canHoId,
                    'vai_tro'         => 2,
                    'ngay_chuyen_den' => '2023-01-01 00:00:00',
                    'trang_thai'      => 1,
                    'nguoi_cap_nhat'  => 1,
                    'createdAt'       => now(),
                    'updatedAt'       => now(),
                ];
                $cuDanIndex++;
            }
        }

        foreach ($data as $item) {
            $exists = DB::table('cu_dan_can_ho')
                ->where('cu_dan', $item['cu_dan'])
                ->where('can_ho', $item['can_ho'])
                ->exists();
            if (!$exists) {
                DB::table('cu_dan_can_ho')->insert($item);
            }
        }
    }
}
