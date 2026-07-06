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

        foreach ($data as $item) {
            if (!DB::table('vai_tro')->where('vai_tro', $item['vai_tro'])->exists()) {
                DB::table('vai_tro')->insert($item);
            }
        }
    }
}
