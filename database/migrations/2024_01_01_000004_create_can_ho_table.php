<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('can_ho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toa_nha')->nullable()->constrained('toa_nha');
            $table->integer('tinh_trang_so_huu')->nullable();
            $table->string('so_can_ho', 50)->nullable();
            $table->integer('tang')->nullable();
            $table->decimal('dien_tich', 10, 2)->nullable();
            $table->foreignId('trang_thai')->nullable()->constrained('trang_thai_can_ho');
            $table->decimal('gia', 15, 2)->nullable();
            $table->foreignId('loai_can_ho')->nullable()->constrained('loai_can_ho');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('can_ho');
    }
};
