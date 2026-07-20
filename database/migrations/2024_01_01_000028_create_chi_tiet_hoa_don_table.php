<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chi_tiet_hoa_don', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('hoa_don');
            $table->string('ten_phi_dich_vu', 255);
            $table->decimal('don_gia', 15, 2)->default(0);
            $table->integer('chi_so_cu')->nullable();
            $table->integer('chi_so_moi')->nullable();
            $table->decimal('so_luong', 15, 2)->default(0);
            $table->decimal('thanh_tien', 15, 2)->default(0);
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('hoa_don', 'fk_cthd_hoadon')->references('id')->on('hoa_don');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_hoa_don');
    }
};
