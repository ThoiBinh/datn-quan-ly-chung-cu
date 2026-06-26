<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cu_dan', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ho_ten_dem', 150);
            $table->string('ten', 50);
            $table->string('sdt', 15)->nullable();
            $table->string('cccd', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('mat_khau', 255)->nullable();
            $table->dateTime('ngay_sinh')->nullable();
            $table->tinyInteger('gioi_tinh')->nullable();
            $table->string('tinh', 100)->nullable();
            $table->string('xa', 100)->nullable();
            $table->string('dia_chi', 255)->nullable();
            $table->tinyInteger('trang_thai')->default(1)->comment('1: đang cư trú, 2: Tạm vắng, 3: Đã chuyển đi');
            $table->integer('nguoi_cap_nhat')->nullable();
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deletedAt')->nullable();

            $table->foreign('nguoi_cap_nhat', 'fk_cudan_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cu_dan');
    }
};
