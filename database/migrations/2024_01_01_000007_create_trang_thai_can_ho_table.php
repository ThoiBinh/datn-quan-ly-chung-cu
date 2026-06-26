<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trang_thai_can_ho', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ten_trang_thai', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trang_thai_can_ho');
    }
};
