<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tien_ich', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ten_tien_ich', 255);
            $table->integer('loai_tien_ich');
            $table->integer('toa_nha')->nullable();
            $table->text('mo_ta')->nullable();
            $table->string('vi_tri', 255)->nullable();
            $table->integer('suc_chua')->nullable();
            $table->time('gio_mo_cua')->nullable();
            $table->time('gio_dong_cua')->nullable();
            $table->decimal('phi_su_dung', 15, 2)->default(0);
            $table->boolean('can_dat_truoc')->default(false);
            $table->text('hinh_url')->nullable();
            $table->integer('trang_thai')->default(1);
            $table->integer('nguoi_cap_nhat')->nullable();
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deletedAt')->nullable();

            $table->foreign('loai_tien_ich', 'fk_ti_loai')->references('id')->on('loai_tien_ich');
            $table->foreign('toa_nha', 'fk_ti_toanha')->references('id')->on('toa_nha');
            $table->foreign('nguoi_cap_nhat', 'fk_ti_capnhat')->references('id')->on('nhan_vien');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tien_ich');
    }
};
