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
            DB::table('loai_tinh_phi_dich_vu')->updateOrInsert(
                ['ten_loai' => $item],
                ['ten_loai' => $item]
            );
        }
    }
}
