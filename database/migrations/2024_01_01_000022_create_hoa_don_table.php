<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoa_don', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ma_thanh_toan', 100);
            $table->integer('can_ho');
            $table->integer('thang');
            $table->integer('nam');
            $table->decimal('tong_tien', 15, 2)->default(0);
            $table->decimal('so_tien_da_thanh_toan', 15, 2)->default(0);
            $table->decimal('chi_phi', 15, 2)->default(0);
            $table->dateTime('han_thanh_toan')->nullable();
            $table->integer('trang_thai')->default(1);
            $table->integer('nguoi_cap_nhat')->nullable();
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('can_ho', 'fk_hd_canho')->references('id')->on('can_ho');
            $table->foreign('nguoi_cap_nhat', 'fk_hd_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoa_don');
    }
};
