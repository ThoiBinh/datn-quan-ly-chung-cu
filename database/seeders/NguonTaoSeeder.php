<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NguonTaoSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['Hệ thống', 'Thủ công', 'Cổng thanh toán'];

        foreach ($data as $item) {
            DB::table('nguon_tao')->updateOrInsert(
                ['ten_nguon_tao' => $item],
                ['ten_nguon_tao' => $item]
            );
        }
    }
}
