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
            ['ten_thuoc_tinh' => 'Tình trạng nội thất', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Số ban công', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Hướng cửa chính', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Diện tích sàn (m²)', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Số phòng khách', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Chiều cao trần (m)', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Có thang máy', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Chỗ để xe riêng', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Năm bàn giao', 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_thuoc_tinh' => 'Mã căn hộ theo CĐT', 'createdAt' => now(), 'updatedAt' => now()],
        ];

        foreach ($data as $item) {
            if (!DB::table('thuoc_tinh')->where('ten_thuoc_tinh', $item['ten_thuoc_tinh'])->exists()) {
                DB::table('thuoc_tinh')->insert($item);
            }
        }
    }
}
