<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TienIchSeeder extends Seeder
{
    public function run(): void
    {
        // loai_tien_ich: 1=Thể thao, 2=Giải trí, 3=Tiện ích chung, 4=Sự kiện, 5=Khác
        $data = [
            [
                'ten_tien_ich'    => 'Hồ bơi',
                'loai_tien_ich'   => 1,
                'mo_ta'           => 'Hồ bơi ngoài trời',
                'vi_tri'          => 'Tầng trệt, khu B',
                'suc_chua'        => 50,
                'gio_mo_cua'      => '06:00:00',
                'gio_dong_cua'    => '21:00:00',
                'phi_su_dung'     => 0,
                'can_dat_truoc'   => false,
            ],
            [
                'ten_tien_ich'    => 'Phòng Gym',
                'loai_tien_ich'   => 1,
                'mo_ta'           => 'Phòng tập thể hình',
                'vi_tri'          => 'Tầng 2',
                'suc_chua'        => 30,
                'gio_mo_cua'      => '05:00:00',
                'gio_dong_cua'    => '22:00:00',
                'phi_su_dung'     => 0,
                'can_dat_truoc'   => false,
            ],
            [
                'ten_tien_ich'    => 'Phòng sinh hoạt cộng đồng',
                'loai_tien_ich'   => 4,
                'mo_ta'           => 'Tổ chức sự kiện, họp',
                'vi_tri'          => 'Tầng 3',
                'suc_chua'        => 80,
                'gio_mo_cua'      => '08:00:00',
                'gio_dong_cua'    => '22:00:00',
                'phi_su_dung'     => 200000,
                'can_dat_truoc'   => true,
            ],
            [
                'ten_tien_ich'    => 'Sân tennis',
                'loai_tien_ich'   => 1,
                'mo_ta'           => 'Sân tennis tiêu chuẩn',
                'vi_tri'          => 'Khu thể thao',
                'suc_chua'        => 4,
                'gio_mo_cua'      => '06:00:00',
                'gio_dong_cua'    => '21:00:00',
                'phi_su_dung'     => 100000,
                'can_dat_truoc'   => true,
            ],
        ];

        foreach ($data as $item) {
            if (!DB::table('tien_ich')->where('ten_tien_ich', $item['ten_tien_ich'])->exists()) {
                DB::table('tien_ich')->insert(array_merge($item, [
                    'trang_thai' => 1,
                    'createdAt'  => now(),
                    'updatedAt'  => now(),
                ]));
            }
        }
    }
}
