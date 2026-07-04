<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThuocTinhCanHoSeeder extends Seeder
{
    public function run(): void
    {
        // thuoc_tinh: 1=Diện tích, 2=Số phòng ngủ, 3=Số phòng tắm, 4=Hướng ban công, 5=Tầng
        // kieu_du_lieu: 1=int, 2=string, 3=datetime
        $canHoIds = DB::table('can_ho')->pluck('id')->toArray();

        $huong = ['Đông', 'Tây', 'Nam', 'Bắc', 'Đông Nam', 'Đông Bắc'];
        $dienTichTheoLoai = [1 => 35, 2 => 55, 3 => 80, 4 => 110, 5 => 180];

        $data = [];
        foreach ($canHoIds as $canHoId) {
            $canHo = DB::table('can_ho')->where('id', $canHoId)->first();
            $loai  = $canHo->loai_can_ho;
            $dientich = $dienTichTheoLoai[$loai] ?? 50;

            $exists = DB::table('thuoc_tinh_can_ho')
                ->where('can_ho', $canHoId)
                ->where('thuoc_tinh', 1)
                ->exists();

            if ($exists) continue;

            $data[] = ['can_ho' => $canHoId, 'thuoc_tinh' => 1, 'gia_tri_thuoc_tinh' => (string)$dientich, 'kieu_du_lieu' => 1, 'createdAt' => now(), 'updatedAt' => now()];
            $data[] = ['can_ho' => $canHoId, 'thuoc_tinh' => 2, 'gia_tri_thuoc_tinh' => (string)max(1, $loai - 1), 'kieu_du_lieu' => 1, 'createdAt' => now(), 'updatedAt' => now()];
            $data[] = ['can_ho' => $canHoId, 'thuoc_tinh' => 3, 'gia_tri_thuoc_tinh' => (string)max(1, (int)floor($loai / 2)), 'kieu_du_lieu' => 1, 'createdAt' => now(), 'updatedAt' => now()];
            $data[] = ['can_ho' => $canHoId, 'thuoc_tinh' => 4, 'gia_tri_thuoc_tinh' => $huong[$canHoId % count($huong)], 'kieu_du_lieu' => 2, 'createdAt' => now(), 'updatedAt' => now()];
            $data[] = ['can_ho' => $canHoId, 'thuoc_tinh' => 5, 'gia_tri_thuoc_tinh' => (string)$canHo->tang, 'kieu_du_lieu' => 1, 'createdAt' => now(), 'updatedAt' => now()];
        }

        if (!empty($data)) {
            DB::table('thuoc_tinh_can_ho')->insert($data);
        }
    }
}
