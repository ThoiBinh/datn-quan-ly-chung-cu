<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cu_dan_can_ho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cu_dan')->nullable()->constrained('cu_dan');
            $table->foreignId('can_ho')->nullable()->constrained('can_ho');
            $table->foreignId('vai_tro')->nullable()->constrained('vai_tro');
            $table->datetime('ngay_chuyen_den')->nullable();
            $table->datetime('ngay_chuyen_di')->nullable();
            $table->integer('trang_thai')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cu_dan_can_ho');
    }
};
