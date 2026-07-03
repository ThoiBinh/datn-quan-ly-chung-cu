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

        DB::table('loai_phuong_tien')->insertOrIgnore($data);
    }
}
