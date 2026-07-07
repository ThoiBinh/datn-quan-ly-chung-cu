<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThuocTinhSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_thuoc_tinh' => 'Diện tích (m²)'],
            ['ten_thuoc_tinh' => 'Số phòng ngủ'],
            ['ten_thuoc_tinh' => 'Số phòng tắm'],
            ['ten_thuoc_tinh' => 'Hướng ban công'],
            ['ten_thuoc_tinh' => 'Tầng'],
        ];

        foreach ($data as $item) {
            if (!DB::table('thuoc_tinh')->where('ten_thuoc_tinh', $item['ten_thuoc_tinh'])->exists()) {
                DB::table('thuoc_tinh')->insert(array_merge($item, [
                    'createdAt' => now(),
                    'updatedAt' => now(),
                ]));
            }
        }
    }
}
