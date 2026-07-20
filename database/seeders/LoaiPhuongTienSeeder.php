<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiPhuongTienSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['Ô tô', 'Xe máy', 'Xe đạp điện', 'Xe đạp'];

            foreach ($data as $item) {
            if (!DB::table('loai_phuong_tien')
                ->where('ten_loai_phuong_tien', $item)
                ->exists()) {

                DB::table('loai_phuong_tien')->insert([
                    'ten_loai_phuong_tien' => $item,
                ]);
            }
    }
    }
}
