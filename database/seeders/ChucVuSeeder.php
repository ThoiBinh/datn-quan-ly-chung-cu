<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChucVuSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['Quản lý', 'Kế toán', 'Lễ tân', 'Kỹ thuật', 'Bảo vệ', 'Admin'];

        foreach ($data as $item) {
            DB::table('chuc_vu')->updateOrInsert(
                ['chuc_vu' => $item],
                ['chuc_vu' => $item, 'createdAt' => now(), 'updatedAt' => now()]
            );
        }
    }
}
