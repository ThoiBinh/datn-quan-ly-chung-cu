<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_bao', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de');
            $table->text('noi_dung')->nullable();
            $table->foreignId('nguoi_tao')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes('deletedAt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_bao');
    }
};
