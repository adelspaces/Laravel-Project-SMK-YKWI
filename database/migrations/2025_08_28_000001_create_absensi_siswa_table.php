<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiSiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absensi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_absensi_id')->constrained('master_absensi')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->enum('status', ['hadir', 'izin', 'sakit']);
            $table->boolean('is_teacher_validated')->default(false);
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate entries
            $table->unique(['master_absensi_id', 'siswa_id']);
            
            // Add index for better query performance
            $table->index(['siswa_id', 'master_absensi_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absensi_siswa');
    }
}