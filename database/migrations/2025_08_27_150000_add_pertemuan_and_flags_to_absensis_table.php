<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPertemuanAndFlagsToAbsensisTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->integer('pertemuan')->nullable()->after('status');
            $table->boolean('is_student_submitted')->default(false)->after('pertemuan');
            $table->boolean('is_teacher_edited')->default(false)->after('is_student_submitted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['pertemuan', 'is_student_submitted', 'is_teacher_edited']);
        });
    }
}