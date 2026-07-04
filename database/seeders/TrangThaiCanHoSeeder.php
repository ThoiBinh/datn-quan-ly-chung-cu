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
            DB::table('trang_thai_can_ho')->updateOrInsert(
                ['ten_trang_thai' => $item],
                ['ten_trang_thai' => $item]
            );
        }
    }
}
