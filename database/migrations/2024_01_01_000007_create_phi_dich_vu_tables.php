<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phi_dich_vu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loai_phi_dich_vu')->nullable()->constrained('loai_phi_dich_vu');
            $table->string('ten_phi_dich_vu');
            $table->decimal('don_gia', 15, 2)->nullable();
            $table->foreignId('don_vi_tinh')->nullable()->constrained('don_vi_tinh_phi_dich_vu');
            $table->foreignId('loai_tinh_phi')->nullable()->constrained('loai_tinh_phi_dich_vu');
            $table->timestamps();
        });

        Schema::create('can_ho_phi_dich_vu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('can_ho')->nullable()->constrained('can_ho');
            $table->foreignId('phi_dich_vu')->nullable()->constrained('phi_dich_vu');
            $table->decimal('don_gia', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('can_ho_phi_dich_vu');
        Schema::dropIfExists('phi_dich_vu');
    }
};
