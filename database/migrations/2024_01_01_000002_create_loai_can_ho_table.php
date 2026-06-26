<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loai_can_ho', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ten_loai_can_ho', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loai_can_ho');
    }
};
