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
            ['ten_loai_phi_dich_vu' => 'Phí thang máy'],
            ['ten_loai_phi_dich_vu' => 'Phí an ninh'],
            ['ten_loai_phi_dich_vu' => 'Phí hồ bơi'],
            ['ten_loai_phi_dich_vu' => 'Phí gym'],
            ['ten_loai_phi_dich_vu' => 'Phí cây xanh'],
            ['ten_loai_phi_dich_vu' => 'Phí cứu hỏa'],
            ['ten_loai_phi_dich_vu' => 'Phí bảo hiểm tòa nhà'],
            ['ten_loai_phi_dich_vu' => 'Phí dịch vụ khác'],
        ];

        foreach ($data as $item) {
            DB::table('loai_phi_dich_vu')->updateOrInsert(
                ['ten_loai_phi_dich_vu' => $item['ten_loai_phi_dich_vu']],
                $item
            );
        }
    }
}
