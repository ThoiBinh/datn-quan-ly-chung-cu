<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('don_vi_tinh_phi_dich_vu', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('don_vi', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('don_vi_tinh_phi_dich_vu');
    }
};
