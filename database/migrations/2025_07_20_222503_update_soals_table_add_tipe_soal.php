<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->enum('tipe_soal', ['pilihan_ganda', 'esai'])->after('pertanyaan');
        });
    }

    public function down()
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->dropColumn('tipe_soal');
        });
    }
};
