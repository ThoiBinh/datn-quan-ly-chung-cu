<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChucVuSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['chuc_vu' => 'Quản lý'],
            ['chuc_vu' => 'Kế toán'],
            ['chuc_vu' => 'Lễ tân'],
            ['chuc_vu' => 'Kỹ thuật'],
            ['chuc_vu' => 'Bảo vệ'],
            ['chuc_vu' => 'Admin'],
        ];

        foreach ($data as $item) {
            DB::table('chuc_vu')->insertOrIgnore(array_merge($item, [
                'createdAt' => now(),
                'updatedAt' => now(),
            ]));
        }
    }
}
