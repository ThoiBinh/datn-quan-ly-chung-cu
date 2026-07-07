<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrangThaiCanHoSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['Có sẵn', 'Đang ở', 'Đang sửa chữa', 'Đã bàn giao'];

        foreach ($data as $item) {
            if (!DB::table('trang_thai_can_ho')->where('ten_trang_thai', $item['ten_trang_thai'])->exists()) {
                DB::table('trang_thai_can_ho')->insert($item);
            }
        }
    }
}
