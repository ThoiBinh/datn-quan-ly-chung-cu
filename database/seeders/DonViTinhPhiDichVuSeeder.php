<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonViTinhPhiDichVuSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['kWh', 'm³', 'Tháng', 'Xe', 'm²'];

        foreach ($data as $donVi) {
            DB::table('don_vi_tinh_phi_dich_vu')->updateOrInsert(
                ['don_vi' => $donVi],
                ['don_vi' => $donVi]
            );
        }
    }
}
