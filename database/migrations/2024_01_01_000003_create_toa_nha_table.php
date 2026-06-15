<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toa_nha', function (Blueprint $table) {
            $table->id();
            $table->string('ten_toa_nha');
            $table->string('dia_chi')->nullable();
            $table->integer('so_tang')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toa_nha');
    }
};
