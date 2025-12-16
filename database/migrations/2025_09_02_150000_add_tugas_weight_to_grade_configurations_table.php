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
        Schema::table('grade_configurations', function (Blueprint $table) {
            $table->decimal('tugas_weight', 5, 2)->default(0.00)->after('assessment_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_configurations', function (Blueprint $table) {
            $table->dropColumn('tugas_weight');
        });
    }
};
