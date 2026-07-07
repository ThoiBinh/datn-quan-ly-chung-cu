<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhiDichVuSeeder extends Seeder
{
    public function run(): void
    {
        $donVi   = DB::table('don_vi_tinh_phi_dich_vu')->pluck('id', 'don_vi')->toArray();
        $loaiTinh = DB::table('loai_tinh_phi_dich_vu')->pluck('id', 'ten_loai')->toArray();
        $loaiPhi  = DB::table('loai_phi_dich_vu')->pluck('id', 'ten_loai_phi_dich_vu')->toArray();

        $data = [
            ['loai' => 'Phí quản lý',  'ten' => 'Phí quản lý căn hộ',        'don_gia' => 15000,   'don_vi' => 'm²',    'tinh_phi' => 'Theo diện tích'],
            ['loai' => 'Điện',          'ten' => 'Tiền điện',                  'don_gia' => 3500,    'don_vi' => 'kWh',   'tinh_phi' => 'Theo chỉ số'],
            ['loai' => 'Nước',          'ten' => 'Tiền nước',                  'don_gia' => 15000,   'don_vi' => 'm³',    'tinh_phi' => 'Theo chỉ số'],
            ['loai' => 'Gửi xe',        'ten' => 'Phí gửi xe ô tô',           'don_gia' => 1200000, 'don_vi' => 'Xe',    'tinh_phi' => 'Theo số lượng'],
            ['loai' => 'Gửi xe',        'ten' => 'Phí gửi xe máy',            'don_gia' => 200000,  'don_vi' => 'Xe',    'tinh_phi' => 'Theo số lượng'],
            ['loai' => 'Internet',      'ten' => 'Phí internet cáp quang',    'don_gia' => 200000,  'don_vi' => 'Tháng', 'tinh_phi' => 'Cố định'],
            ['loai' => 'Vệ sinh',       'ten' => 'Phí vệ sinh',               'don_gia' => 50000,   'don_vi' => 'Tháng', 'tinh_phi' => 'Cố định'],
            ['loai' => 'Bảo trì',       'ten' => 'Phí bảo trì chung cư',     'don_gia' => 10000,   'don_vi' => 'm²',    'tinh_phi' => 'Theo diện tích'],
            ['loai' => 'Phí thang máy', 'ten' => 'Phí thang máy',            'don_gia' => 30000,   'don_vi' => 'Tháng', 'tinh_phi' => 'Cố định'],
            ['loai' => 'Phí an ninh',   'ten' => 'Phí an ninh 24/7',         'don_gia' => 100000,  'don_vi' => 'Tháng', 'tinh_phi' => 'Cố định'],
            ['loai' => 'Phí hồ bơi',   'ten' => 'Phí hồ bơi',               'don_gia' => 150000,  'don_vi' => 'Tháng', 'tinh_phi' => 'Cố định'],
            ['loai' => 'Phí gym',       'ten' => 'Phí phòng gym',            'don_gia' => 200000,  'don_vi' => 'Tháng', 'tinh_phi' => 'Cố định'],
            ['loai' => 'Phí cây xanh',  'ten' => 'Phí cây xanh cảnh quan',  'don_gia' => 20000,   'don_vi' => 'Tháng', 'tinh_phi' => 'Cố định'],
            ['loai' => 'Phí cứu hỏa',   'ten' => 'Phí phòng cháy chữa cháy','don_gia' => 25000,   'don_vi' => 'Tháng', 'tinh_phi' => 'Cố định'],
            ['loai' => 'Gửi xe',        'ten' => 'Phí gửi xe đạp điện',     'don_gia' => 100000,  'don_vi' => 'Xe',    'tinh_phi' => 'Theo số lượng'],
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
