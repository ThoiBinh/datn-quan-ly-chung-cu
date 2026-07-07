<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonViTinhPhiDichVuSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['kWh', 'm³', 'Tháng', 'Xe', 'm²'];

        foreach ($data as $item) {
            if (!DB::table('don_vi_tinh_phi_dich_vu')->where('don_vi', $item['don_vi'])->exists()) {
                DB::table('don_vi_tinh_phi_dich_vu')->insert($item);
            }
        }
    }
}
