<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loai_tien_ich', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ten_loai_tien_ich', 100);
            $table->dateTime('deletedAt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loai_tien_ich');
    }
};
