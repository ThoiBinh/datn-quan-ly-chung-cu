<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CuDan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {   
        if (User::where('email', 'admin@chungcu.vn')->exists()) {
            return;
        }

        User::create([
            'name' => 'Admin Hệ thống',
            'email' => 'admin@chungcu.vn',
            'phone' => '0900000001',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Ban Quản Lý',
            'email' => 'manager@chungcu.vn',
            'phone' => '0900000002',
            'password' => Hash::make('12345678'),
            'role' => 'manager',
            'status' => 'active',
        ]);

        $resident1 = User::create([
            'name' => 'Nguyễn Văn An',
            'email' => 'cudan1@chungcu.vn',
            'phone' => '0901234567',
            'password' => Hash::make('12345678'),
            'role' => 'resident',
            'status' => 'active',
        ]);

        CuDan::create([
            'user_id' => $resident1->id,
            'ho_ten' => 'Nguyễn Văn An',
            'nam_sinh' => '1985-03-15',
            'cccd' => '001085012345',
            'sdt' => '0901234567',
            'email' => 'cudan1@chungcu.vn',
        ]);

        $resident2 = User::create([
            'name' => 'Trần Thị Bình',
            'email' => 'cudan2@chungcu.vn',
            'phone' => '0909876543',
            'password' => Hash::make('12345678'),
            'role' => 'resident',
            'status' => 'active',
        ]);

        CuDan::create([
            'user_id' => $resident2->id,
            'ho_ten' => 'Trần Thị Bình',
            'nam_sinh' => '1990-07-22',
            'cccd' => '002090098765',
            'sdt' => '0909876543',
            'email' => 'cudan2@chungcu.vn',
        ]);
    }
}
