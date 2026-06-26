<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_bao_da_doc', function (Blueprint $table) {
            $table->integer('thong_bao_id');
            $table->integer('cu_dan_id');
            $table->dateTime('read_at')->useCurrent();

            $table->primary(['thong_bao_id', 'cu_dan_id']);

            $table->foreign('thong_bao_id', 'fk_tbdd_tb')->references('id')->on('thong_bao');
            $table->foreign('cu_dan_id', 'fk_tbdd_cudan')->references('id')->on('cu_dan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_bao_da_doc');
    }
};
