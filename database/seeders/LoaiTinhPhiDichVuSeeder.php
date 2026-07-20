<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiTinhPhiDichVuSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['Cố định', 'Theo chỉ số', 'Theo diện tích', 'Theo số lượng'];

        foreach ($data as $item) {
        if (!DB::table('loai_tinh_phi_dich_vu')
            ->where('ten_loai', $item)
            ->exists()) {

            DB::table('loai_tinh_phi_dich_vu')->insert([
                'ten_loai' => $item,
            ]);
        }
    }
    }
}
