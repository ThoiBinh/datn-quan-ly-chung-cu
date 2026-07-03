<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonViTinhPhiDichVuSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['don_vi' => 'kWh'],
            ['don_vi' => 'm³'],
            ['don_vi' => 'Tháng'],
            ['don_vi' => 'Xe'],
            ['don_vi' => 'm²'],
        ];

        DB::table('don_vi_tinh_phi_dich_vu')->insertOrIgnore($data);
    }
}
