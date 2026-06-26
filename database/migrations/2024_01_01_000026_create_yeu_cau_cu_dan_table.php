<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yeu_cau_cu_dan', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('cu_dan');
            $table->integer('loai_yeu_cau');
            $table->string('tieu_de', 255);
            $table->text('noi_dung')->nullable();
            $table->dateTime('ngay_gui')->useCurrent();
            $table->integer('muc_do_uu_tien')->default(1);
            $table->integer('trang_thai')->default(1);
            $table->integer('nhan_vien_xu_ly')->nullable();
            $table->dateTime('ngay_hoan_thanh')->nullable();
            $table->integer('nguoi_cap_nhat')->nullable();
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('cu_dan', 'fk_yccd_cudan')->references('id')->on('cu_dan');
            $table->foreign('loai_yeu_cau', 'fk_yccd_loai')->references('id')->on('loai_yeu_cau');
            $table->foreign('nhan_vien_xu_ly', 'fk_yccd_nvxuly')->references('id')->on('nhan_vien');
            $table->foreign('nguoi_cap_nhat', 'fk_yccd_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_cu_dan');
    }
};
