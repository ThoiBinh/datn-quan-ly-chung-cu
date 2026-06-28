<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThuocTinhSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_thuoc_tinh' => 'Diện tích (m²)', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Số phòng ngủ', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Số phòng tắm', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Hướng ban công', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Tầng', 'createdAt' => now(), 'updatedAt' => now()],
        ];

        DB::table('thuoc_tinh')->insertOrIgnore($data);
    }
}
