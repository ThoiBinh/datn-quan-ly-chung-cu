<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thuoc_tinh', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ten_thuoc_tinh', 150);
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deletedAt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thuoc_tinh');
    }
};
