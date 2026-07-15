<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToaNhaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'ten_toa_nha' => 'Tòa A',
                'tien_to' => 'A',
                'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM',
                'so_tang' => 20,
            ],
            [
                'ten_toa_nha' => 'Tòa B',
                'tien_to' => 'B',
                'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM',
                'so_tang' => 22,
            ],
            [
                'ten_toa_nha' => 'Tòa C',
                'tien_to' => 'C',
                'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM',
                'so_tang' => 18,
            ],
            [
                'ten_toa_nha' => 'Tòa D',
                'tien_to' => 'D',
                'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM',
                'so_tang' => 25,
            ],
            [
                'ten_toa_nha' => 'Tòa E',
                'tien_to' => 'E',
                'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM',
                'so_tang' => 30,
            ],
            [
                'ten_toa_nha' => 'Tòa F',
                'tien_to' => 'F',
                'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM',
                'so_tang' => 16,
            ],
            [
                'ten_toa_nha' => 'Tòa G',
                'tien_to' => 'G',
                'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM',
                'so_tang' => 28,
            ],
            [
                'ten_toa_nha' => 'Tòa H',
                'tien_to' => 'H',
                'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM',
                'so_tang' => 24,
            ],
            [
                'ten_toa_nha' => 'Tòa I',
                'tien_to' => 'I',
                'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM',
                'so_tang' => 21,
            ],
            [
                'ten_toa_nha' => 'Tòa J',
                'tien_to' => 'J',
                'dia_chi' => '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM',
                'so_tang' => 26,
            ],
        ];

        foreach ($data as $item) {
            DB::table('toa_nha')->updateOrInsert(
                ['tien_to' => $item['tien_to']],
                array_merge($item, [
                    'createdAt' => now(),
                    'updatedAt' => now(),
                    'deletedAt' => null,
                ])
            );
        }
    }
}
