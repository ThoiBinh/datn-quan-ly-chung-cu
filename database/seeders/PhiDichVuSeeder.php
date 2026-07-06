<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhiDichVuSeeder extends Seeder
{
    public function run(): void
    {
        // loai_phi_dich_vu: 1=Quản lý, 2=Điện, 3=Nước, 4=Gửi xe, 5=Internet, 6=Vệ sinh, 7=Bảo trì
        // don_vi_tinh: 1=kWh, 2=m³, 3=Tháng, 4=Xe, 5=m²
        // loai_tinh_phi: 1=Cố định, 2=Theo chỉ số, 3=Theo diện tích, 4=Theo số lượng
        $data = [
            ['loai_phi_dich_vu' => 1, 'ten_phi_dich_vu' => 'Phí quản lý căn hộ',   'don_gia' => 15000,  'don_vi_tinh' => 5, 'loai_tinh_phi' => 3],
            ['loai_phi_dich_vu' => 2, 'ten_phi_dich_vu' => 'Tiền điện',             'don_gia' => 3500,   'don_vi_tinh' => 1, 'loai_tinh_phi' => 2],
            ['loai_phi_dich_vu' => 3, 'ten_phi_dich_vu' => 'Tiền nước',             'don_gia' => 15000,  'don_vi_tinh' => 2, 'loai_tinh_phi' => 2],
            ['loai_phi_dich_vu' => 4, 'ten_phi_dich_vu' => 'Phí gửi xe ô tô',      'don_gia' => 1200000,'don_vi_tinh' => 4, 'loai_tinh_phi' => 4],
            ['loai_phi_dich_vu' => 4, 'ten_phi_dich_vu' => 'Phí gửi xe máy',       'don_gia' => 200000, 'don_vi_tinh' => 4, 'loai_tinh_phi' => 4],
            ['loai_phi_dich_vu' => 5, 'ten_phi_dich_vu' => 'Phí internet',          'don_gia' => 200000, 'don_vi_tinh' => 3, 'loai_tinh_phi' => 1],
            ['loai_phi_dich_vu' => 6, 'ten_phi_dich_vu' => 'Phí vệ sinh',           'don_gia' => 50000,  'don_vi_tinh' => 3, 'loai_tinh_phi' => 1],
            ['loai_phi_dich_vu' => 7, 'ten_phi_dich_vu' => 'Phí bảo trì chung cư', 'don_gia' => 10000,  'don_vi_tinh' => 5, 'loai_tinh_phi' => 3],
        ];

        foreach ($data as $item) {
            if (!DB::table('phi_dich_vu')->where('ten_phi_dich_vu', $item['ten_phi_dich_vu'])->exists()) {
                DB::table('phi_dich_vu')->insert(array_merge($item, [
                    'nguoi_cap_nhat' => 1,
                    'createdAt'      => now(),
                    'updatedAt'      => now(),
                ]));
            }
        }
    }
}
