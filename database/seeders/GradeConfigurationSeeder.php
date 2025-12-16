<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GradeConfiguration;

class GradeConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GradeConfiguration::firstOrCreate([
            'attendance_weight' => 50,
            'assessment_weight' => 50,
            'grade_thresholds' => [
                "A" => 80,
                "A-" => 75,
                "B+" => 70,
                "B" => 60,
                "B-" => 55,
                "C+" => 50,
                "C" => 40,
                "C-" => 35,
                "D" => 30,
                "E" => 0
            ]
        ]);
    }
}