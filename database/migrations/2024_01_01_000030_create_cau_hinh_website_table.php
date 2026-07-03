<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cau_hinh_website', function (Blueprint $table) {
            $table->id();
            $table->string('ma_thuoc_tinh', 100);
            $table->string('ten_thuoc_tinh', 255);
            $table->longText('gia_tri')->nullable();
            $table->enum('kieu_du_lieu', [
                'text', 'textarea', 'number', 'email', 'phone', 'url',
                'image', 'file', 'password', 'boolean', 'json',
            ])->default('text');
            $table->string('ma_nhom', 50);
            $table->string('ten_nhom', 100);
            $table->string('mo_ta', 500)->nullable();
            $table->string('placeholder', 255)->nullable();
            $table->integer('thu_tu')->default(0);
            $table->boolean('la_bao_mat')->default(false);
            $table->boolean('duoc_chinh_sua')->default(true);
            $table->boolean('trang_thai')->default(true);
            $table->timestamps();

            $table->unique('ma_thuoc_tinh');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cau_hinh_website');
    }
};
