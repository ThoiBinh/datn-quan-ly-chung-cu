<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lich_su_thanh_toan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoa_don')->nullable()->constrained('hoa_don');
            $table->datetime('ngay_thanh_toan')->nullable();
            $table->decimal('so_tien', 15, 2)->nullable();
            $table->string('phuong_thuc_thanh_toan')->nullable();
            $table->string('ma_giao_dich')->nullable();
            $table->foreignId('nguoi_thanh_toan')->nullable()->constrained('cu_dan');
            $table->string('ghi_chu')->nullable();
            $table->foreignId('nguon_tao')->nullable()->constrained('nguon_tao');
            $table->timestamp('createdAt')->nullable();
        });

        Schema::create('cau_hinh_thanh_toan', function (Blueprint $table) {
            $table->id();
            $table->string('loai_phuong_thuc')->nullable();
            $table->string('ten_nha_cung_cap')->nullable();
            $table->string('dinh_danh_thu_huong')->nullable();
            $table->string('ma_nhan_dien')->nullable();
            $table->string('ten_chu_tai_khoan')->nullable();
            $table->integer('trang_thai')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cau_hinh_thanh_toan');
        Schema::dropIfExists('lich_su_thanh_toan');
    }
};
