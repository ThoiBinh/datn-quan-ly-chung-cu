<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phuong_tien', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ten_phuong_tien', 150)->nullable();
            $table->string('bien_so', 20);
            $table->integer('loai_phuong_tien');
            $table->integer('can_ho');
            $table->dateTime('ngay_dang_ky')->nullable();
            $table->dateTime('ngay_huy')->nullable();
            $table->integer('trang_thai')->default(1);
            $table->integer('nguoi_cap_nhat')->nullable();

            $table->foreign('loai_phuong_tien', 'fk_pt_loai')->references('id')->on('loai_phuong_tien');
            $table->foreign('can_ho', 'fk_pt_canho')->references('id')->on('can_ho');
            $table->foreign('nguoi_cap_nhat', 'fk_pt_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phuong_tien');
    }
};
