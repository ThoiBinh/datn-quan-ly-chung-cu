<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nguon_tao', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ten_nguon_tao', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nguon_tao');
    }
};
