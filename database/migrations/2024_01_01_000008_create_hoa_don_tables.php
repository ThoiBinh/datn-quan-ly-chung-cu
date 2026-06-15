<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoa_don', function (Blueprint $table) {
            $table->id();
            $table->string('ma_thanh_toan')->nullable()->unique();
            $table->foreignId('can_ho')->nullable()->constrained('can_ho');
            $table->integer('thang');
            $table->integer('nam');
            $table->decimal('tong_tien', 15, 2)->default(0);
            $table->decimal('so_tien_da_thanh_toan', 15, 2)->default(0);
            $table->decimal('chi_phi', 15, 2)->default(0);
            $table->datetime('han_thanh_toan')->nullable();
            $table->integer('trang_thai')->default(1);
            $table->timestamps();
        });

        Schema::create('chi_tiet_hoa_don', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoa_don')->nullable()->constrained('hoa_don');
            $table->foreignId('phi_dich_vu')->nullable()->constrained('phi_dich_vu');
            $table->decimal('don_gia', 15, 2)->nullable();
            $table->integer('chi_so_cu')->nullable();
            $table->integer('chi_so_moi')->nullable();
            $table->decimal('so_luong', 10, 2)->nullable();
            $table->decimal('thanh_tien', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_hoa_don');
        Schema::dropIfExists('hoa_don');
    }
};
