<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_bao', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('tieu_de', 255);
            $table->string('noi_dung', 1000)->nullable();
            $table->integer('nguoi_tao');
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deletedAt')->nullable();

            $table->foreign('nguoi_tao', 'fk_tb_nguoitao')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_bao');
    }
};
