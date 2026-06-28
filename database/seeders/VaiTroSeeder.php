<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VaiTroSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['vai_tro' => 'Chủ hộ'],
            ['vai_tro' => 'Thành viên'],
            ['vai_tro' => 'Người thuê'],
        ];

        DB::table('vai_tro')->insertOrIgnore($data);
    }
}
