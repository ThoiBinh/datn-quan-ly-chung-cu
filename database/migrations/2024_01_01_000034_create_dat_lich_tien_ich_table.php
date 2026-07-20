<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dat_lich_tien_ich', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('ma_dat_lich', 50);
            $table->integer('cu_dan');
            $table->integer('can_ho')->nullable();
            $table->integer('tien_ich');
            $table->dateTime('thoi_gian_bat_dau');
            $table->dateTime('thoi_gian_ket_thuc');
            $table->integer('so_nguoi')->default(1);
            $table->decimal('phi_su_dung', 15, 2)->default(0);
            $table->string('ghi_chu', 500)->nullable();
            $table->integer('trang_thai')->default(1)
                ->comment('1: Chờ duyệt, 2: Đã duyệt, 3: Từ chối, 4: Đã hủy, 5: Hoàn thành');
            $table->integer('nhan_vien_duyet')->nullable();
            $table->dateTime('ngay_duyet')->nullable();
            $table->dateTime('ngay_huy')->nullable();
            $table->string('ly_do_huy', 500)->nullable();
            $table->integer('nguoi_cap_nhat')->nullable();
            $table->dateTime('createdAt')->useCurrent()->comment('Thời điểm cư dân đặt lịch');
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deletedAt')->nullable();

            $table->unique('ma_dat_lich', 'uq_dltl_madatlich');
            $table->index(['tien_ich', 'thoi_gian_bat_dau', 'thoi_gian_ket_thuc'], 'idx_dltl_tienich_thoigian');
            $table->index(['cu_dan', 'thoi_gian_bat_dau'], 'idx_dltl_cudan_thoigian');

            $table->foreign('cu_dan', 'fk_dltl_cudan')->references('id')->on('cu_dan');
            $table->foreign('can_ho', 'fk_dltl_canho')->references('id')->on('can_ho');
            $table->foreign('tien_ich', 'fk_dltl_tienich')->references('id')->on('tien_ich');
            $table->foreign('nhan_vien_duyet', 'fk_dltl_nvduyet')->references('id')->on('nhan_vien');
            $table->foreign('nguoi_cap_nhat', 'fk_dltl_capnhat')->references('id')->on('nhan_vien');
        });

        // SQLite (dùng cho test suite) không hỗ trợ ALTER TABLE ... ADD CONSTRAINT.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE dat_lich_tien_ich ADD CONSTRAINT chk_dltl_songuoi CHECK (so_nguoi > 0)');
            DB::statement('ALTER TABLE dat_lich_tien_ich ADD CONSTRAINT chk_dltl_thoigian CHECK (thoi_gian_ket_thuc > thoi_gian_bat_dau)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dat_lich_tien_ich');
    }
};
