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
        Schema::table('jawaban_siswas', function (Blueprint $table) {
            if (!Schema::hasColumn('jawaban_siswas', 'status_penilaian')) {
                $table->string('status_penilaian')->default('pending')->after('jawaban');
            }
            
            if (!Schema::hasColumn('jawaban_siswas', 'feedback')) {
                $table->text('feedback')->nullable()->after('status_penilaian');
            }
            
            if (!Schema::hasColumn('jawaban_siswas', 'nilai')) {
                $table->decimal('nilai', 5, 2)->nullable()->after('feedback');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jawaban_siswas', function (Blueprint $table) {
            if (Schema::hasColumn('jawaban_siswas', 'status_penilaian')) {
                $table->dropColumn('status_penilaian');
            }
            
            if (Schema::hasColumn('jawaban_siswas', 'feedback')) {
                $table->dropColumn('feedback');
            }
            
            if (Schema::hasColumn('jawaban_siswas', 'nilai')) {
                $table->dropColumn('nilai');
            }
        });
    }
};