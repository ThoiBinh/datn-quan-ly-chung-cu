<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cau_hinh_thanh_toan', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('loai_phuong_thuc', 100);
            $table->string('ten_nha_cung_cap', 150)->nullable();
            $table->string('dinh_danh_thu_huong', 150)->nullable();
            $table->string('ma_nhan_dien', 150)->nullable();
            $table->string('ten_chu_tai_khoan', 150)->nullable();
            $table->integer('trang_thai')->default(1);
            $table->integer('nguoi_cap_nhat')->nullable();
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('nguoi_cap_nhat', 'fk_chtt_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cau_hinh_thanh_toan');
    }
};
