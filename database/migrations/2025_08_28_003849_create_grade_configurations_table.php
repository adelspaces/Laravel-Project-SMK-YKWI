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
        Schema::create('grade_configurations', function (Blueprint $table) {
            $table->id();
            $table->decimal('attendance_weight', 5, 2)->default(0.00);
            $table->decimal('assessment_weight', 5, 2)->default(0.00);
            $table->json('grade_thresholds');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_configurations');
    }
};