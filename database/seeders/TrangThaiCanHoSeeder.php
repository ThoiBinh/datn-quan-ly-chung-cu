<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrangThaiCanHoSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_trang_thai' => 'Có sẵn'],
            ['ten_trang_thai' => 'Đang ở'],
            ['ten_trang_thai' => 'Đang sửa chữa'],
            ['ten_trang_thai' => 'Đã bàn giao'],
        ];

        foreach ($data as $item) {
            if (!DB::table('trang_thai_can_ho')->where('ten_trang_thai', $item['ten_trang_thai'])->exists()) {
                DB::table('trang_thai_can_ho')->insert($item);
            }
        }
    }
}
