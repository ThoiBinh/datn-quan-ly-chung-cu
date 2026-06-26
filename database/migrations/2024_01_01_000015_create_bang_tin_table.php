<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bang_tin', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('tieu_de', 120);
            $table->text('noi_dung');
            $table->text('hinh_url')->nullable();
            $table->integer('nguoi_tao');
            $table->integer('nguoi_cap_nhat');
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deletedAt')->nullable();

            $table->foreign('nguoi_tao', 'fk_bt_nguoitao')->references('id')->on('nhan_vien');
            $table->foreign('nguoi_cap_nhat', 'fk_bt_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bang_tin');
    }
};
