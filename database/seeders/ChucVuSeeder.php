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
            if (!DB::table('chuc_vu')->where('chuc_vu', $item['chuc_vu'])->exists()) {
                DB::table('chuc_vu')->insert(array_merge($item, [
                    'createdAt' => now(),
                    'updatedAt' => now(),
                ]));
            }
        }
    }
}
