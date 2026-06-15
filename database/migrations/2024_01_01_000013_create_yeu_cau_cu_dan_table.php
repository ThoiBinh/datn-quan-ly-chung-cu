<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yeu_cau_cu_dan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cu_dan')->nullable()->constrained('cu_dan');
            $table->string('tieu_de');
            $table->text('noi_dung')->nullable();
            $table->datetime('ngay_gui')->nullable();
            $table->integer('muc_do_uu_tien')->default(1);
            $table->integer('trang_thai')->default(1);
            $table->foreignId('nhan_vien_xu_ly')->nullable()->constrained('users');
            $table->datetime('ngay_hoan_thanh')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_cu_dan');
    }
};
