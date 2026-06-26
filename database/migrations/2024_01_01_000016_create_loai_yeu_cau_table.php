<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loai_yeu_cau', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('name', 120);
            $table->integer('nguoi_cap_nhat');
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deletedAt')->nullable();

            $table->foreign('nguoi_cap_nhat', 'fk_lyc_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loai_yeu_cau');
    }
};
