<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('can_ho', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('toa_nha');
            $table->string('so_can_ho', 50);
            $table->integer('tang');
            $table->integer('trang_thai');
            $table->decimal('gia', 15, 2)->nullable();
            $table->integer('loai_can_ho');
            $table->integer('nguoi_cap_nhat')->nullable();
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('toa_nha', 'fk_canho_toanha')->references('id')->on('toa_nha');
            $table->foreign('trang_thai', 'fk_canho_trangthai')->references('id')->on('trang_thai_can_ho');
            $table->foreign('loai_can_ho', 'fk_canho_loai')->references('id')->on('loai_can_ho');
            $table->foreign('nguoi_cap_nhat', 'fk_canho_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('can_ho');
    }
};
