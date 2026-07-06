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

        foreach ($data as $item) {
            if (!DB::table('loai_phi_dich_vu')->where('ten_loai_phi_dich_vu', $item['ten_loai_phi_dich_vu'])->exists()) {
                DB::table('loai_phi_dich_vu')->insert($item);
            }
        }
    }
}
