<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chuc_vu', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('chuc_vu', 100);
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deletedAt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chuc_vu');
    }
};
