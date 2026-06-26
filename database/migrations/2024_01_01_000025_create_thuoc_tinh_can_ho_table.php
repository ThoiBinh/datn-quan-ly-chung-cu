<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thuoc_tinh_can_ho', function (Blueprint $table) {
            $table->integer('can_ho');
            $table->integer('thuoc_tinh');
            $table->string('gia_tri_thuoc_tinh', 150)->nullable();
            $table->integer('kieu_du_lieu')->comment('1:int, 2:string, 3:datetime');
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->primary(['can_ho', 'thuoc_tinh']);

            $table->foreign('can_ho', 'fk_ttch_canho')->references('id')->on('can_ho');
            $table->foreign('thuoc_tinh', 'fk_ttch_thuoctinh')->references('id')->on('thuoc_tinh');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thuoc_tinh_can_ho');
    }
};
