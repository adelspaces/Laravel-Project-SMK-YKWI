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
        Schema::create('jawaban_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // siswa yang menjawab
            $table->foreignId('kuis_ujian_id')->constrained()->onDelete('cascade'); // kuis/ujian yang sedang dikerjakan
            $table->foreignId('banksoal_id')->constrained()->onDelete('cascade'); // soal yang dijawab
            $table->string('jawaban'); // jawaban yang dipilih siswa (opsi_a/b/c/d dst)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_siswas');
    }
};
