<?php

namespace App\Services;

use App\Models\User;
use App\Models\Absensi;
use App\Models\HasilUjian;
use App\Models\GradeConfiguration;
use App\Models\GradeResult;

class GradeCalculationService
{
    public function calculateGradesForStudent($studentId)
    {
        // Get the global grade configuration
        $config = GradeConfiguration::first();

        if (!$config) {
            throw new \Exception('Grade configuration not found');
        }

        // Calculate attendance score
        $attendanceScore = $this->calculateAttendanceScore($studentId, $config->attendance_weight);

        // Calculate assessment (exam/quiz) and tugas (assignments) scores separately
        $assessmentScore = $this->calculateAssessmentScore($studentId, $config->assessment_weight);
        $tugasScore = $this->calculateTugasScore($studentId, $config->tugas_weight);

        // Calculate final score (sum of weighted components)
        $finalScore = $attendanceScore + $assessmentScore + $tugasScore;

        // Assign letter grade
        $letterGrade = $this->assignLetterGrade($finalScore, $config->grade_thresholds);

        // Save the result
        $gradeResult = GradeResult::updateOrCreate(
            ['user_id' => $studentId],
            [
                'attendance_score' => $attendanceScore,
                'assessment_score' => $assessmentScore + $tugasScore,
                'final_score' => $finalScore,
                'letter_grade' => $letterGrade,
                'calculated_at' => now()
            ]
        );

        return $gradeResult;
    }

    private function calculateAttendanceScore($studentId, $weight)
    {
        // Get total attendance records for student
        $totalAttendance = Absensi::where('siswa_id', $studentId)->count();

        if ($totalAttendance == 0) {
            return 0;
        }

        // Get present attendance records
        $presentAttendance = Absensi::where('siswa_id', $studentId)
            ->where('status', 'hadir')
            ->count();

        // Calculate attendance percentage
        $attendancePercentage = ($presentAttendance / $totalAttendance) * 100;

        // Apply weight to get weighted attendance score
        $weightedAttendanceScore = $attendancePercentage * ($weight / 100);

        return $weightedAttendanceScore;
    }

    private function calculateAssessmentScore($studentId, $weight)
    {
        // Exam/quiz results only
        $examResults = HasilUjian::where('user_id', $studentId)->get();
        if ($examResults->isEmpty()) {
            return 0;
        }

        $averageAssessmentScore = $examResults->avg('nilai_total');
        return $averageAssessmentScore * ($weight / 100);
    }

    private function calculateTugasScore($studentId, $weight)
    {
        $user = User::find($studentId);
        if (!$user || !$user->nis) {
            return 0;
        }
        $siswa = \App\Models\Siswa::where('nis', $user->nis)->first();
        if (!$siswa) {
            return 0;
        }
        $tugasScores = \App\Models\Jawaban::where('siswa_id', $siswa->id)
            ->whereNotNull('nilai')
            ->pluck('nilai');
        if ($tugasScores->isEmpty()) {
            return 0;
        }
        $averageTugasScore = $tugasScores->avg();
        return $averageTugasScore * ($weight / 100);
    }

    private function assignLetterGrade($score, $thresholds)
    {
        // Default thresholds if none are configured
        if (!$thresholds) {
            $thresholds = [
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
            ];
        }

        // Sort thresholds in descending order
        arsort($thresholds);

        foreach ($thresholds as $grade => $threshold) {
            if ($score >= $threshold) {
                return $grade;
            }
        }

        // Default to lowest grade
        return 'E';
    }
}
