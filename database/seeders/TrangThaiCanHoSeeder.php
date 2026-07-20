<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrangThaiCanHoSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['Đang hoạt động', 'Đang bảo trì'];

       foreach ($data as $item) {
        if (!DB::table('trang_thai_can_ho')
            ->where('ten_trang_thai', $item)
            ->exists()) {

            DB::table('trang_thai_can_ho')->insert([
                'ten_trang_thai' => $item,
            ]);
        }
    }
    }
}
