<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toa_nha', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ten_toa_nha', 255);
            $table->string('dia_chi', 255);
            $table->integer('so_tang')->default(1);
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toa_nha');
    }
};
