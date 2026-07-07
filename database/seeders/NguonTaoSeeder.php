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
            ['ten_nguon_tao' => 'VNPay'],
            ['ten_nguon_tao' => 'Momo'],
        ];

        foreach ($data as $item) {
            if (!DB::table('nguon_tao')->where('ten_nguon_tao', $item['ten_nguon_tao'])->exists()) {
                DB::table('nguon_tao')->insert($item);
            }
        }
    }
}
