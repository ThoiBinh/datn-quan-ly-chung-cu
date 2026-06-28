<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiCanHoSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_loai_can_ho' => 'Studio'],
            ['ten_loai_can_ho' => '1 Phòng ngủ'],
            ['ten_loai_can_ho' => '2 Phòng ngủ'],
            ['ten_loai_can_ho' => '3 Phòng ngủ'],
            ['ten_loai_can_ho' => 'Penthouse'],
        ];

        DB::table('loai_can_ho')->insertOrIgnore($data);
    }
}
