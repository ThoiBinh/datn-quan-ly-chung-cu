<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cu_dan_can_ho', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('cu_dan');
            $table->integer('can_ho');
            $table->integer('vai_tro');
            $table->dateTime('ngay_chuyen_den')->nullable();
            $table->dateTime('ngay_chuyen_di')->nullable();
            $table->integer('trang_thai')->default(1);
            $table->integer('nguoi_cap_nhat')->nullable();
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('cu_dan', 'fk_cdch_cudan')->references('id')->on('cu_dan');
            $table->foreign('can_ho', 'fk_cdch_canho')->references('id')->on('can_ho');
            $table->foreign('vai_tro', 'fk_cdch_vaitro')->references('id')->on('vai_tro');
            $table->foreign('nguoi_cap_nhat', 'fk_cdch_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cu_dan_can_ho');
    }
};
