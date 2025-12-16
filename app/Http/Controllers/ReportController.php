<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HasilUjian;
use App\Models\KuisUjian;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\JawabanSiswa;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentReportExport;
use App\Exports\ClassReportExport;

class ReportController extends Controller
{
    // Student Individual Report
    public function studentReport(Request $request, $studentId = null)
    {
        $user = Auth::user();
        
        // If no student ID provided and user is a student, use their own ID
        if (!$studentId && $user->role === 'siswa') {
            $studentId = $user->siswa->id ?? $user->id;
        }

        $student = $studentId ? User::find($studentId) : null;
        $kelas = Kelas::all();
        $mapel = Mapel::all();

        // Filter parameters
        $selectedKelas = $request->input('kelas_id');
        $selectedMapel = $request->input('mapel_id');
        $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $reportData = [];
        if ($student) {
            $reportData = $this->generateStudentReport($student, $selectedKelas, $selectedMapel, $startDate, $endDate);
        }

        return view('pages.reports.student', compact(
            'student', 'kelas', 'mapel', 'reportData', 
            'selectedKelas', 'selectedMapel', 'startDate', 'endDate'
        ));
    }

    // Class Report
    public function classReport(Request $request)
    {
        $kelas = Kelas::all();
        $mapel = Mapel::all();

        $selectedKelas = $request->input('kelas_id');
        $selectedMapel = $request->input('mapel_id');
        $startDate = $request->input('start_date', Carbon::now()->subMonths(1)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $reportData = [];
        if ($selectedKelas && $selectedMapel) {
            $reportData = $this->generateClassReport($selectedKelas, $selectedMapel, $startDate, $endDate);
        }

        return view('pages.reports.class', compact(
            'kelas', 'mapel', 'reportData',
            'selectedKelas', 'selectedMapel', 'startDate', 'endDate'
        ));
    }

    // Teacher Report
    public function teacherReport(Request $request)
    {
        $guru = Auth::user()->guru;
        if (!$guru) {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya untuk guru.');
        }

        $mapel = Mapel::all();
        $selectedMapel = $request->input('mapel_id');
        $startDate = $request->input('start_date', Carbon::now()->subMonths(1)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $reportData = [];
        if ($selectedMapel) {
            $reportData = $this->generateTeacherReport($guru->id, $selectedMapel, $startDate, $endDate);
        }

        return view('pages.reports.teacher', compact(
            'mapel', 'reportData', 'selectedMapel', 'startDate', 'endDate'
        ));
    }

    // Generate detailed student report
    private function generateStudentReport($student, $kelasId, $mapelId, $startDate, $endDate)
    {
        $query = HasilUjian::with(['kuisUjian.mapel', 'kuisUjian.guru'])
            ->where('user_id', $student->id)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($kelasId) {
            $query->whereHas('kuisUjian', function($q) use ($kelasId) {
                // Add kelas filter if needed
            });
        }

        if ($mapelId) {
            $query->whereHas('kuisUjian', function($q) use ($mapelId) {
                $q->where('mapel_id', $mapelId);
            });
        }

        $examResults = $query->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $totalExams = $examResults->count();
        $averageScore = $examResults->avg('nilai_total') ?? 0;
        $highestScore = $examResults->max('nilai_total') ?? 0;
        $lowestScore = $examResults->min('nilai_total') ?? 0;

        // Performance by subject
        $subjectPerformance = $examResults->groupBy('kuisUjian.mapel.nama')
            ->map(function($results) {
                return [
                    'total_exams' => $results->count(),
                    'average_score' => $results->avg('nilai_total'),
                    'latest_score' => $results->first()->nilai_total ?? 0
                ];
            });

        // Performance trends (last 6 months)
        $performanceTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthExams = $examResults->filter(function($result) use ($month) {
                return $result->created_at->format('Y-m') === $month->format('Y-m');
            });
            
            $performanceTrend[] = [
                'month' => $month->format('M Y'),
                'average_score' => $monthExams->avg('nilai_total') ?? 0,
                'exam_count' => $monthExams->count()
            ];
        }

        // Attendance correlation (if available)
        $attendanceRate = 0;
        if ($student->siswa) {
            $totalAttendance = Absensi::where('siswa_id', $student->siswa->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->count();
            
            $presentAttendance = Absensi::where('siswa_id', $student->siswa->id)
                ->where('status', 'hadir')
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->count();
                
            $attendanceRate = $totalAttendance > 0 ? ($presentAttendance / $totalAttendance) * 100 : 0;
        }

        return [
            'exam_results' => $examResults,
            'statistics' => [
                'total_exams' => $totalExams,
                'average_score' => round($averageScore, 2),
                'highest_score' => $highestScore,
                'lowest_score' => $lowestScore,
                'attendance_rate' => round($attendanceRate, 2)
            ],
            'subject_performance' => $subjectPerformance,
            'performance_trend' => $performanceTrend
        ];
    }

    // Generate class report
    private function generateClassReport($kelasId, $mapelId, $startDate, $endDate)
    {
        $kelas = Kelas::find($kelasId);
        $mapel = Mapel::find($mapelId);
        
        // Get all students in the class
        $students = User::whereHas('siswa', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        })->get();

        // Get exam results for the class
        $examResults = HasilUjian::with(['user', 'kuisUjian'])
            ->whereIn('user_id', $students->pluck('id'))
            ->whereHas('kuisUjian', function($q) use ($mapelId) {
                $q->where('mapel_id', $mapelId);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Class statistics
        $classAverage = $examResults->avg('nilai_total') ?? 0;
        $highestScore = $examResults->max('nilai_total') ?? 0;
        $lowestScore = $examResults->min('nilai_total') ?? 0;
        $passRate = $examResults->where('nilai_total', '>=', 70)->count() / max($examResults->count(), 1) * 100;

        // Student rankings
        $studentRankings = $examResults->groupBy('user_id')
            ->map(function($results, $userId) {
                $user = User::find($userId);
                return [
                    'student' => $user,
                    'average_score' => $results->avg('nilai_total'),
                    'exam_count' => $results->count(),
                    'latest_score' => $results->sortByDesc('created_at')->first()->nilai_total ?? 0
                ];
            })
            ->sortByDesc('average_score')
            ->values();

        // Question analysis
        $questionAnalysis = $this->analyzeQuestions($examResults);

        return [
            'kelas' => $kelas,
            'mapel' => $mapel,
            'students' => $students,
            'exam_results' => $examResults,
            'statistics' => [
                'class_average' => round($classAverage, 2),
                'highest_score' => $highestScore,
                'lowest_score' => $lowestScore,
                'pass_rate' => round($passRate, 2),
                'total_students' => $students->count(),
                'total_exams' => $examResults->count()
            ],
            'student_rankings' => $studentRankings,
            'question_analysis' => $questionAnalysis
        ];
    }

    // Generate teacher report
    private function generateTeacherReport($guruId, $mapelId, $startDate, $endDate)
    {
        $examResults = HasilUjian::with(['user', 'kuisUjian'])
            ->whereHas('kuisUjian', function($q) use ($guruId, $mapelId) {
                $q->where('guru_id', $guruId)
                  ->where('mapel_id', $mapelId);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $uniqueStudents = $examResults->pluck('user_id')->unique()->count();
        $averageScore = $examResults->avg('nilai_total') ?? 0;
        $passRate = $examResults->where('nilai_total', '>=', 70)->count() / max($examResults->count(), 1) * 100;

        // Class performance comparison
        $classPerformance = $examResults->groupBy(function($result) {
            return $result->user->siswa->kelas->nama ?? 'Unknown';
        })->map(function($results) {
            return [
                'average_score' => $results->avg('nilai_total'),
                'student_count' => $results->pluck('user_id')->unique()->count(),
                'exam_count' => $results->count()
            ];
        });

        // Question effectiveness
        $questionEffectiveness = $this->analyzeQuestionEffectiveness($guruId, $mapelId, $startDate, $endDate);

        return [
            'statistics' => [
                'total_students' => $uniqueStudents,
                'total_exams' => $examResults->count(),
                'average_score' => round($averageScore, 2),
                'pass_rate' => round($passRate, 2)
            ],
            'class_performance' => $classPerformance,
            'question_effectiveness' => $questionEffectiveness,
            'exam_results' => $examResults->take(20) // Latest 20 results
        ];
    }

    // Analyze questions for difficulty and effectiveness
    private function analyzeQuestions($examResults)
    {
        $questionStats = [];
        
        foreach ($examResults as $result) {
            $answers = JawabanSiswa::where('kuis_ujian_id', $result->kuis_ujian_id)
                ->where('user_id', $result->user_id)
                ->with('banksoal')
                ->get();
                
            foreach ($answers as $answer) {
                $questionId = $answer->banksoal_id;
                if (!isset($questionStats[$questionId])) {
                    $questionStats[$questionId] = [
                        'question' => $answer->banksoal,
                        'total_attempts' => 0,
                        'correct_attempts' => 0
                    ];
                }
                
                $questionStats[$questionId]['total_attempts']++;
                if ($answer->is_correct) {
                    $questionStats[$questionId]['correct_attempts']++;
                }
            }
        }

        // Calculate difficulty
        foreach ($questionStats as &$stat) {
            $stat['success_rate'] = $stat['total_attempts'] > 0 
                ? ($stat['correct_attempts'] / $stat['total_attempts']) * 100 
                : 0;
            $stat['difficulty'] = $this->getDifficultyLevel($stat['success_rate']);
        }

        return $questionStats;
    }

    private function analyzeQuestionEffectiveness($guruId, $mapelId, $startDate, $endDate)
    {
        // Implementation for question effectiveness analysis
        return [];
    }

    private function getDifficultyLevel($successRate)
    {
        if ($successRate >= 80) return 'Mudah';
        if ($successRate >= 60) return 'Sedang';
        if ($successRate >= 40) return 'Sulit';
        return 'Sangat Sulit';
    }

    // Export methods
    public function exportStudentReport($studentId, Request $request)
    {
        $student = User::findOrFail($studentId);
        $reportData = $this->generateStudentReport(
            $student,
            $request->kelas_id,
            $request->mapel_id,
            $request->start_date,
            $request->end_date
        );

        $filename = "laporan_siswa_{$student->name}_" . date('Y-m-d') . '.xlsx';
        return Excel::download(new StudentReportExport($student, $reportData), $filename);
    }

    public function exportClassReport(Request $request)
    {
        $reportData = $this->generateClassReport(
            $request->kelas_id,
            $request->mapel_id,
            $request->start_date,
            $request->end_date
        );

        $filename = "laporan_kelas_{$reportData['kelas']->nama}_{$reportData['mapel']->nama}_" . date('Y-m-d') . '.xlsx';
        return Excel::download(new ClassReportExport($reportData), $filename);
    }
}