<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hop_dong', function (Blueprint $table) {
            $table->id();
            $table->string('so_hop_dong', 100)->nullable()->unique();
            $table->foreignId('can_ho')->nullable()->constrained('can_ho');
            $table->foreignId('cu_dan')->nullable()->constrained('cu_dan');
            $table->foreignId('loai_hop_dong')->nullable()->constrained('loai_hop_dong');
            $table->datetime('ngay_ky')->nullable();
            $table->datetime('ngay_bat_dau')->nullable();
            $table->datetime('ngay_ket_thuc')->nullable();
            $table->decimal('gia_tri_hop_dong', 15, 2)->nullable();
            $table->decimal('tien_coc', 15, 2)->nullable();
            $table->string('file_dinh_kem')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->integer('trang_thai')->default(4);
            $table->foreignId('nguoi_tao')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hop_dong');
    }
};
