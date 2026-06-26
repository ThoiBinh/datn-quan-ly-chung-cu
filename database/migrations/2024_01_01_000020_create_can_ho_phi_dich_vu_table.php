<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('can_ho_phi_dich_vu', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('can_ho');
            $table->integer('phi_dich_vu');
            $table->decimal('don_gia', 15, 2)->default(0);
            $table->integer('nguoi_cap_nhat')->nullable();
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('can_ho', 'fk_chpdv_canho')->references('id')->on('can_ho');
            $table->foreign('phi_dich_vu', 'fk_chpdv_pdv')->references('id')->on('phi_dich_vu');
            $table->foreign('nguoi_cap_nhat', 'fk_chpdv_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('can_ho_phi_dich_vu');
    }
};
