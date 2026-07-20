<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiTienIchSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_loai_tien_ich' => 'Thể thao'],
            ['ten_loai_tien_ich' => 'Giải trí'],
            ['ten_loai_tien_ich' => 'Tiện ích chung'],
            ['ten_loai_tien_ich' => 'Sự kiện'],
            ['ten_loai_tien_ich' => 'Khác'],
        ];

        foreach ($data as $item) {
            if (!DB::table('loai_tien_ich')->where('ten_loai_tien_ich', $item['ten_loai_tien_ich'])->exists()) {
                DB::table('loai_tien_ich')->insert($item);
            }
        }
    }
}
