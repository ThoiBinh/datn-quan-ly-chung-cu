<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CanHoPhiDichVuSeeder extends Seeder
{
    public function run(): void
    {
        $canHoIds    = DB::table('can_ho')->pluck('id')->toArray();
        $phiDichVuIds = DB::table('phi_dich_vu')->pluck('id', 'ten_phi_dich_vu')->toArray();

        // Phí áp dụng cho tất cả căn hộ: quản lý, điện, nước, vệ sinh, bảo trì
        $phiChung = ['Phí quản lý căn hộ', 'Tiền điện', 'Tiền nước', 'Phí vệ sinh', 'Phí bảo trì chung cư'];

        foreach ($canHoIds as $canHoId) {
            foreach ($phiChung as $tenPhi) {
                if (!isset($phiDichVuIds[$tenPhi])) continue;
                $phiId = $phiDichVuIds[$tenPhi];
                $donGia = DB::table('phi_dich_vu')->where('id', $phiId)->value('don_gia');

                $exists = DB::table('can_ho_phi_dich_vu')
                    ->where('can_ho', $canHoId)
                    ->where('phi_dich_vu', $phiId)
                    ->exists();

                if (!$exists) {
                    DB::table('can_ho_phi_dich_vu')->insert([
                        'can_ho'         => $canHoId,
                        'phi_dich_vu'    => $phiId,
                        'don_gia'        => $donGia,
                        'nguoi_cap_nhat' => 1,
                        'createdAt'      => now(),
                        'updatedAt'      => now(),
                    ]);
                }
            }
        }
    }
}
