<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed chuc_vu 'Admin' nếu chưa có (dùng name lookup để không conflict với data gốc)
        $this->seedAdminChucVu();

        $this->call(UserSeeder::class);
    }

    private function seedAdminChucVu(): void
    {
        if (!DB::table('chuc_vu')->where('chuc_vu', 'Admin')->exists()) {
            DB::table('chuc_vu')->insert([
                'chuc_vu'   => 'Admin',
                'createdAt' => now(),
                'updatedAt' => now(),
            ]);
        }
    }
}
