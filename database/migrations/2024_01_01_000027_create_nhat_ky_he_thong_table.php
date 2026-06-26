<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nhat_ky_he_thong', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('nguoi_thuc_hien')->nullable();
            $table->dateTime('thoi_gian')->useCurrent();
            $table->string('hanh_dong', 50);
            $table->string('bang_tac_dong', 100);
            $table->integer('id_ban_ghi')->nullable();
            $table->text('gia_tri_cu')->nullable();
            $table->text('gia_tri_moi')->nullable();
            $table->dateTime('createdAt')->useCurrent();

            $table->foreign('nguoi_thuc_hien', 'fk_nkht_nguoi')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhat_ky_he_thong');
    }
};
