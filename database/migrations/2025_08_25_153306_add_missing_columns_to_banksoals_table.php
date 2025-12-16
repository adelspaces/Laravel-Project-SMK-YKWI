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
        Schema::table('banksoals', function (Blueprint $table) {
            $table->string('opsi_e')->nullable()->after('opsi_d');
            $table->integer('bobot_nilai')->default(10)->after('kunci_jawaban');
            $table->enum('tingkat_kesulitan', ['mudah', 'sedang', 'sulit'])->default('sedang')->after('bobot_nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banksoals', function (Blueprint $table) {
            $table->dropColumn(['opsi_e', 'bobot_nilai', 'tingkat_kesulitan']);
        });
    }
};
