<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('banksoal_kuis_ujian', function (Blueprint $table) {
            $table->unsignedBigInteger('banksoal_id');
            $table->unsignedBigInteger('kuis_ujian_id');

            $table->foreign('banksoal_id')->references('id')->on('banksoals')->onDelete('cascade');
            $table->foreign('kuis_ujian_id')->references('id')->on('kuis_ujians')->onDelete('cascade');

            $table->primary(['banksoal_id', 'kuis_ujian_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banksoal_kuis_ujian');
    }
};
