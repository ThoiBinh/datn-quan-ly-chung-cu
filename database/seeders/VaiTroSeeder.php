<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VaiTroSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['Chủ hộ', 'Thành viên', 'Người thuê'];

        foreach ($data as $item) {
            DB::table('vai_tro')->updateOrInsert(
                ['vai_tro' => $item],
                ['vai_tro' => $item]
            );
        }
    }
}
