<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiPhiDichVuSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_loai_phi_dich_vu' => 'Phí quản lý'],
            ['ten_loai_phi_dich_vu' => 'Điện'],
            ['ten_loai_phi_dich_vu' => 'Nước'],
            ['ten_loai_phi_dich_vu' => 'Gửi xe'],
            ['ten_loai_phi_dich_vu' => 'Internet'],
            ['ten_loai_phi_dich_vu' => 'Vệ sinh'],
            ['ten_loai_phi_dich_vu' => 'Bảo trì'],
        ];

        DB::table('loai_phi_dich_vu')->insertOrIgnore($data);
    }
}
