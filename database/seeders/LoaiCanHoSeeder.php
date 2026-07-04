<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiCanHoSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['Studio', '1 Phòng ngủ', '2 Phòng ngủ', '3 Phòng ngủ', 'Penthouse'];

        foreach ($data as $item) {
            DB::table('loai_can_ho')->updateOrInsert(
                ['ten_loai_can_ho' => $item],
                ['ten_loai_can_ho' => $item]
            );
        }
    }
}
