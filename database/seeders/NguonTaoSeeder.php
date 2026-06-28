<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NguonTaoSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_nguon_tao' => 'Hệ thống'],
            ['ten_nguon_tao' => 'Thủ công'],
            ['ten_nguon_tao' => 'Cổng thanh toán'],
        ];

        DB::table('nguon_tao')->insertOrIgnore($data);
    }
}
