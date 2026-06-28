<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CanHoSeeder extends Seeder
{
    public function run(): void
    {
        // toa_nha: 1=Tòa A, 2=Tòa B, 3=Tòa C
        // trang_thai_can_ho: 1=Có sẵn, 2=Đang ở, 3=Đang sửa, 4=Đã bàn giao
        // loai_can_ho: 1=Studio, 2=1PN, 3=2PN, 4=3PN, 5=Penthouse
        $data = [];
        $canHoIndex = 1;

        $toaNha = [
            1 => ['prefix' => 'A', 'tang' => 10, 'so_can_moi_tang' => 5],
            2 => ['prefix' => 'B', 'tang' => 10, 'so_can_moi_tang' => 5],
            3 => ['prefix' => 'C', 'tang' => 8,  'so_can_moi_tang' => 5],
        ];

        $giaTheoLoai = [1 => 1500000000, 2 => 2200000000, 3 => 3500000000, 4 => 5000000000, 5 => 8000000000];

        foreach ($toaNha as $toaNhaId => $info) {
            for ($tang = 1; $tang <= $info['tang']; $tang++) {
                for ($can = 1; $can <= $info['so_can_moi_tang']; $can++) {
                    if ($canHoIndex > 50) break 3;

                    $loai = (($canHoIndex - 1) % 5) + 1;
                    $trangThai = $canHoIndex <= 40 ? 2 : 1; // 40 đang ở, 10 còn trống

                    $data[] = [
                        'toa_nha'        => $toaNhaId,
                        'so_can_ho'      => $info['prefix'] . '-' . str_pad($tang, 2, '0', STR_PAD_LEFT) . str_pad($can, 2, '0', STR_PAD_LEFT),
                        'tang'           => $tang,
                        'trang_thai'     => $trangThai,
                        'gia'            => $giaTheoLoai[$loai],
                        'loai_can_ho'    => $loai,
                        'nguoi_cap_nhat' => 1,
                        'createdAt'      => now(),
                        'updatedAt'      => now(),
                    ];
                    $canHoIndex++;
                }
            }
        }

        foreach ($data as $item) {
            if (!DB::table('can_ho')->where('so_can_ho', $item['so_can_ho'])->exists()) {
                DB::table('can_ho')->insert($item);
            }
        }
    }
}
