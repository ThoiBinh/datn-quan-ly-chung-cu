<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiYeuCauSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Sửa chữa'],
            ['name' => 'Khiếu nại'],
            ['name' => 'Hỏi đáp'],
            ['name' => 'Đăng ký phương tiện'],
            ['name' => 'Cấp thẻ cư dân'],
            ['name' => 'Đăng ký tạm trú'],
            ['name' => 'Yêu cầu vệ sinh'],
            ['name' => 'Báo hỏng thiết bị'],
            ['name' => 'Phản ánh an ninh'],
            ['name' => 'Đề xuất cải thiện'],
            ['name' => 'Yêu cầu bảo trì'],
            ['name' => 'Thắc mắc hóa đơn'],
        ];

        foreach ($data as $item) {
            if (!DB::table('loai_yeu_cau')->where('name', $item['name'])->exists()) {
                DB::table('loai_yeu_cau')->insert(array_merge($item, [
                    'nguoi_cap_nhat' => 1,
                    'createdAt'      => now(),
                    'updatedAt'      => now(),
                ]));
            }
        }
    }
}
