<?php

namespace Database\Seeders;

use App\Models\ToaNha;
use App\Models\CanHo;
use App\Models\PhiDichVu;
use Illuminate\Database\Seeder;

class ToaNhaSeeder extends Seeder
{
    public function run(): void
    {
        if (ToaNha::exists()) {
            return;
        }

        $toaA = ToaNha::create([
            'ten_toa_nha' => 'Tòa A',
            'dia_chi' => '12 Nguyễn Văn Linh, Quận 7, TP.HCM',
            'so_tang' => 20,
        ]);

        $toaB = ToaNha::create([
            'ten_toa_nha' => 'Tòa B',
            'dia_chi' => '12 Nguyễn Văn Linh, Quận 7, TP.HCM',
            'so_tang' => 18,
        ]);

        $floor = 1;
        foreach (['A', 'B', 'C', 'D'] as $unit) {
            CanHo::create([
                'toa_nha'   => $toaA->id,
                'so_can_ho' => "A{$floor}0{$unit}",
                'tang'      => $floor,
                'loai_can_ho' => 3,
                'dien_tich' => 75.5,
                'trang_thai' => $unit === 'A' ? 2 : 1,
            ]);
        }

        foreach (['A', 'B', 'C'] as $unit) {
            CanHo::create([
                'toa_nha'   => $toaB->id,
                'so_can_ho' => "B{$floor}0{$unit}",
                'tang'      => $floor,
                'loai_can_ho' => 2,
                'dien_tich' => 55.0,
                'trang_thai' => 1,
            ]);
        }

        PhiDichVu::create([
            'ten_phi_dich_vu' => 'Phí quản lý',
            'loai_phi_dich_vu' => 1,
            'don_gia' => 15000,
            'don_vi_tinh' => 2,
            'loai_tinh_phi' => 3,
        ]);

        PhiDichVu::create([
            'ten_phi_dich_vu' => 'Phí gửi xe máy',
            'loai_phi_dich_vu' => 2,
            'don_gia' => 150000,
            'don_vi_tinh' => 3,
            'loai_tinh_phi' => 1,
        ]);

        PhiDichVu::create([
            'ten_phi_dich_vu' => 'Phí gửi ô tô',
            'loai_phi_dich_vu' => 2,
            'don_gia' => 1500000,
            'don_vi_tinh' => 3,
            'loai_tinh_phi' => 1,
        ]);

        PhiDichVu::create([
            'ten_phi_dich_vu' => 'Tiền điện',
            'loai_phi_dich_vu' => 3,
            'don_gia' => 3500,
            'don_vi_tinh' => 4,
            'loai_tinh_phi' => 2,
        ]);

        PhiDichVu::create([
            'ten_phi_dich_vu' => 'Tiền nước',
            'loai_phi_dich_vu' => 4,
            'don_gia' => 10000,
            'don_vi_tinh' => 5,
            'loai_tinh_phi' => 2,
        ]);
    }
}
