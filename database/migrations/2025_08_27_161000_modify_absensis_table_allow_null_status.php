<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAbsensisTableAllowNullStatus extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Modify status to allow null values
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alfa'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Revert status to not allow null values
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alfa'])->nullable(false)->change();
        });
    }
}