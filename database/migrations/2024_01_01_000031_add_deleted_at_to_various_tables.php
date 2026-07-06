<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'loai_can_ho',
        'loai_phi_dich_vu',
        'don_vi_tinh_phi_dich_vu',
        'loai_tinh_phi_dich_vu',
        'loai_phuong_tien',
        'trang_thai_can_ho',
        'vai_tro',
        'nguon_tao',
        'toa_nha',
        'nhan_vien',
        'can_ho',
        'phi_dich_vu',
        'cau_hinh_thanh_toan',
        'hoa_don',
        'phuong_tien',
        'yeu_cau_cu_dan',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (!Schema::hasColumn($table, 'deletedAt')) {
                    $blueprint->dateTime('deletedAt')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (Schema::hasColumn($table, 'deletedAt')) {
                    $blueprint->dropColumn('deletedAt');
                }
            });
        }
    }
};
