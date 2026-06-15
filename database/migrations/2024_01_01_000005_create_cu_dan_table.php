<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cu_dan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ho_ten');
            $table->string('sdt', 20)->nullable();
            $table->string('cccd', 50)->nullable()->unique();
            $table->string('email')->nullable();
            $table->datetime('nam_sinh')->nullable();
            $table->string('que_quan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cu_dan');
    }
};
