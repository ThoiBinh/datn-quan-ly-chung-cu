<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToaNhaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_toa_nha' => 'Tòa A', 'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM', 'so_tang' => 20],
            ['ten_toa_nha' => 'Tòa B', 'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM', 'so_tang' => 25],
            ['ten_toa_nha' => 'Tòa C', 'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM', 'so_tang' => 18],
        ];

        foreach ($data as $item) {
            DB::table('toa_nha')->updateOrInsert(
                ['ten_toa_nha' => $item['ten_toa_nha']],
                array_merge($item, ['createdAt' => now(), 'updatedAt' => now()])
            );
        }
    }
}
