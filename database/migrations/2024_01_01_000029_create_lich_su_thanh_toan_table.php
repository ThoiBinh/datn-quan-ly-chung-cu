<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lich_su_thanh_toan', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('hoa_don');
            $table->dateTime('ngay_thanh_toan');
            $table->decimal('so_tien', 15, 2)->default(0);
            $table->string('phuong_thuc_thanh_toan', 100)->nullable();
            $table->string('ma_giao_dich', 150)->nullable();
            $table->integer('nguoi_thanh_toan')->nullable();
            $table->string('ghi_chu', 255)->nullable();
            $table->integer('nguon_tao')->nullable();
            $table->dateTime('createdAt')->useCurrent();

            $table->foreign('hoa_don', 'fk_lstt_hoadon')->references('id')->on('hoa_don');
            $table->foreign('nguoi_thanh_toan', 'fk_lstt_nguoitt')->references('id')->on('cu_dan');
            $table->foreign('nguon_tao', 'fk_lstt_nguontao')->references('id')->on('nguon_tao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lich_su_thanh_toan');
    }
};
