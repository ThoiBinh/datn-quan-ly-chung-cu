<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiYeuCauSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Sửa chữa', 'nguoi_cap_nhat' => 1, 'createdAt' => now(), 'updatedAt' => now()],
            ['name' => 'Khiếu nại', 'nguoi_cap_nhat' => 1, 'createdAt' => now(), 'updatedAt' => now()],
            ['name' => 'Hỏi đáp', 'nguoi_cap_nhat' => 1, 'createdAt' => now(), 'updatedAt' => now()],
            ['name' => 'Đăng ký phương tiện', 'nguoi_cap_nhat' => 1, 'createdAt' => now(), 'updatedAt' => now()],
        ];

        DB::table('loai_yeu_cau')->insertOrIgnore($data);
    }
}
