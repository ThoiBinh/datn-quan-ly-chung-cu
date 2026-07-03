<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiTinhPhiDichVuSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_loai' => 'Cố định'],
            ['ten_loai' => 'Theo chỉ số'],
            ['ten_loai' => 'Theo diện tích'],
            ['ten_loai' => 'Theo số lượng'],
        ];

        DB::table('loai_tinh_phi_dich_vu')->insertOrIgnore($data);
    }
}
