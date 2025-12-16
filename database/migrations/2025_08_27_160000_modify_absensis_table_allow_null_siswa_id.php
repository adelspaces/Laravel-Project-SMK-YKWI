<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAbsensisTableAllowNullSiswaId extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Modify siswa_id to allow null values
            $table->unsignedBigInteger('siswa_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Revert siswa_id to not allow null values
            $table->unsignedBigInteger('siswa_id')->nullable(false)->change();
        });
    }
}