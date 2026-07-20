<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nhan_vien', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ho_ten', 150);
            $table->integer('chuc_vu');
            $table->string('sdt', 15)->nullable();
            $table->string('email', 150);
            $table->string('mat_khau', 255);
            $table->integer('trang_thai')->default(1);
            $table->string('ma_nhan_vien', 50);
            $table->string('cccd', 20);
            $table->dateTime('ngay_sinh')->nullable();
            $table->dateTime('ngay_vao_lam')->nullable();
            $table->dateTime('ngay_nghi_lam')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->integer('nguoi_cap_nhat')->nullable();
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->unique('email', 'email');
            $table->unique('ma_nhan_vien', 'ma_nhan_vien');
            $table->unique('cccd', 'cccd');

            $table->foreign('chuc_vu', 'fk_nv_chucvu')->references('id')->on('chuc_vu');
            $table->foreign('nguoi_cap_nhat', 'fk_nv_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhan_vien');
    }
};
