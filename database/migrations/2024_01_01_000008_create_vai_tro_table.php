<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vai_tro', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('vai_tro', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vai_tro');
    }
};
