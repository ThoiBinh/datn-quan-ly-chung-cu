<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiPhuongTienSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_loai_phuong_tien' => 'Ô tô'],
            ['ten_loai_phuong_tien' => 'Xe máy'],
            ['ten_loai_phuong_tien' => 'Xe đạp điện'],
            ['ten_loai_phuong_tien' => 'Xe đạp'],
        ];

        foreach ($data as $item) {
            if (!DB::table('loai_phuong_tien')->where('ten_loai_phuong_tien', $item['ten_loai_phuong_tien'])->exists()) {
                DB::table('loai_phuong_tien')->insert($item);
            }
        }
    }
}
