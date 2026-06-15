<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loai_can_ho', function (Blueprint $table) {
            $table->id();
            $table->string('ten_loai_can_ho');
        });

        Schema::create('trang_thai_can_ho', function (Blueprint $table) {
            $table->id();
            $table->string('ten_trang_thai');
        });

        Schema::create('loai_hop_dong', function (Blueprint $table) {
            $table->id();
            $table->string('ten_loai');
        });

        Schema::create('loai_phi_dich_vu', function (Blueprint $table) {
            $table->id();
            $table->string('ten_loai_phi_dich_vu');
        });

        Schema::create('don_vi_tinh_phi_dich_vu', function (Blueprint $table) {
            $table->id();
            $table->string('don_vi', 50);
        });

        Schema::create('loai_tinh_phi_dich_vu', function (Blueprint $table) {
            $table->id();
            $table->string('ten_loai');
        });

        Schema::create('loai_phuong_tien', function (Blueprint $table) {
            $table->id();
            $table->string('ten_loai_phuong_tien');
        });

        Schema::create('vai_tro', function (Blueprint $table) {
            $table->id();
            $table->string('vai_tro');
        });

        Schema::create('nguon_tao', function (Blueprint $table) {
            $table->id();
            $table->string('ten_nguon_tao');
        });

        Schema::create('chuc_vu', function (Blueprint $table) {
            $table->id();
            $table->string('chuc_vu');
        });

        Schema::create('thuoc_tinh', function (Blueprint $table) {
            $table->id();
            $table->string('ten_thuoc_tinh');
        });

        Schema::create('permission_names', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('permission_actions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_actions');
        Schema::dropIfExists('permission_names');
        Schema::dropIfExists('thuoc_tinh');
        Schema::dropIfExists('chuc_vu');
        Schema::dropIfExists('nguon_tao');
        Schema::dropIfExists('vai_tro');
        Schema::dropIfExists('loai_phuong_tien');
        Schema::dropIfExists('loai_tinh_phi_dich_vu');
        Schema::dropIfExists('don_vi_tinh_phi_dich_vu');
        Schema::dropIfExists('loai_phi_dich_vu');
        Schema::dropIfExists('loai_hop_dong');
        Schema::dropIfExists('trang_thai_can_ho');
        Schema::dropIfExists('loai_can_ho');
    }
};
