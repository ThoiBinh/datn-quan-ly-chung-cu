<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToaNhaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_toa_nha' => 'Tòa A', 'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM', 'so_tang' => 20, 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_toa_nha' => 'Tòa B', 'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM', 'so_tang' => 25, 'createdAt' => now(), 'updatedAt' => now()],
            ['ten_toa_nha' => 'Tòa C', 'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM', 'so_tang' => 18, 'createdAt' => now(), 'updatedAt' => now()],
        ];

        DB::table('toa_nha')->insertOrIgnore($data);
    }
}
