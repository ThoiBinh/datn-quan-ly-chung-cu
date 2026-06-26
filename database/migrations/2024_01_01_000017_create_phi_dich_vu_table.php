<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phi_dich_vu', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('loai_phi_dich_vu');
            $table->string('ten_phi_dich_vu', 150);
            $table->decimal('don_gia', 15, 2)->default(0);
            $table->integer('don_vi_tinh');
            $table->integer('loai_tinh_phi');
            $table->integer('nguoi_cap_nhat')->nullable();
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('loai_phi_dich_vu', 'fk_pdv_loai')->references('id')->on('loai_phi_dich_vu');
            $table->foreign('don_vi_tinh', 'fk_pdv_donvi')->references('id')->on('don_vi_tinh_phi_dich_vu');
            $table->foreign('loai_tinh_phi', 'fk_pdv_loaitinh')->references('id')->on('loai_tinh_phi_dich_vu');
            $table->foreign('nguoi_cap_nhat', 'fk_pdv_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phi_dich_vu');
    }
};
