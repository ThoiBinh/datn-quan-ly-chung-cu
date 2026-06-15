<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phuong_tien', function (Blueprint $table) {
            $table->id();
            $table->string('ten_phuong_tien')->nullable();
            $table->string('bien_so', 50)->nullable();
            $table->foreignId('loai_phuong_tien')->nullable()->constrained('loai_phuong_tien');
            $table->foreignId('can_ho')->nullable()->constrained('can_ho');
            $table->datetime('ngay_dang_ky')->nullable();
            $table->datetime('ngay_huy')->nullable();
            $table->integer('trang_thai')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phuong_tien');
    }
};
