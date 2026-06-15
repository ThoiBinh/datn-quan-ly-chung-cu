<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nhat_ky_he_thong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_thuc_hien')->nullable()->constrained('users');
            $table->datetime('thoi_gian')->nullable();
            $table->string('hanh_dong', 50)->nullable();
            $table->string('bang_tac_dong')->nullable();
            $table->integer('id_ban_ghi')->nullable();
            $table->text('gia_tri_cu')->nullable();
            $table->text('gia_tri_moi')->nullable();
            $table->timestamp('createdAt')->nullable();
        });

        Schema::create('thuoc_tinh_can_ho', function (Blueprint $table) {
            $table->foreignId('can_ho')->nullable()->constrained('can_ho');
            $table->foreignId('thuoc_tinh')->nullable()->constrained('thuoc_tinh');
            $table->string('gia_tri_thuoc_tinh')->nullable();
            $table->timestamps();
        });

        Schema::create('nhan_vien_phan_quyen', function (Blueprint $table) {
            $table->foreignId('nhan_vien')->nullable()->constrained('users');
            $table->foreignId('permission_name')->nullable()->constrained('permission_names');
            $table->foreignId('permission_action')->nullable()->constrained('permission_actions');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhan_vien_phan_quyen');
        Schema::dropIfExists('thuoc_tinh_can_ho');
        Schema::dropIfExists('nhat_ky_he_thong');
    }
};
