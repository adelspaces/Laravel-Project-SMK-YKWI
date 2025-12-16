<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\GradeConfiguration;
use App\Models\GradeResult;
use App\Services\GradeCalculationService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GradeResultsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class GradeController extends Controller
{
    protected $gradeCalculationService;

    public function __construct(GradeCalculationService $gradeCalculationService)
    {
        $this->gradeCalculationService = $gradeCalculationService;
    }

    public function index()
    {
        $config = GradeConfiguration::first();
        $students = User::where('role', 'siswa')->get();

        // Check user role to determine which view to show
        if (Auth::user()->role == 'admin') {
            return view('pages.admin.grades.index', compact('config', 'students'));
        } elseif (Auth::user()->role == 'guru') {
            return view('pages.guru.grades.index', compact('config', 'students'));
        } elseif (Auth::user()->role == 'siswa') {
            // For students, redirect to their grade report
            return redirect()->route('siswa.grades.report');
        }
    }

    public function exportAll()
    {
        $results = GradeResult::with('user')->get();
        $filename = 'nilai_siswa_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new GradeResultsExport($results), $filename);
    }

    public function exportStudent($studentId)
    {
        $result = GradeResult::with('user')->where('user_id', $studentId)->get();
        $student = User::find($studentId);
        $filename = 'nilai_' . ($student ? str_replace(' ', '_', strtolower($student->name)) : 'siswa') . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new GradeResultsExport($result), $filename);
    }

    public function exportAllPdf()
    {
        $results = GradeResult::with('user')->get();
        $pdf = Pdf::loadView('pdf.grades-all', ['results' => $results]);
        return $pdf->download('nilai_siswa_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportStudentPdf($studentId)
    {
        $result = GradeResult::with('user')->where('user_id', $studentId)->first();
        $student = User::find($studentId);
        $pdf = Pdf::loadView('pdf.grades-student', ['result' => $result, 'student' => $student]);
        $filename = 'nilai_' . ($student ? str_replace(' ', '_', strtolower($student->name)) : 'siswa') . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function showConfiguration()
    {
        $config = GradeConfiguration::firstOrCreate([
            'attendance_weight' => 40,
            'assessment_weight' => 40,
            'tugas_weight' => 20,
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

        // Check user role to determine which view to show
        if (Auth::user()->role == 'admin') {
            return view('pages.admin.grades.configuration', compact('config'));
        } elseif (Auth::user()->role == 'guru') {
            return view('pages.guru.grades.configuration', compact('config'));
        } else {
            // For any other role, redirect to grades index
            return redirect()->route('grades.index');
        }
    }

    public function updateConfiguration(Request $request)
    {
        $request->validate([
            'attendance_weight' => 'required|numeric|min:0|max:100',
            'assessment_weight' => 'required|numeric|min:0|max:100',
            'tugas_weight' => 'required|numeric|min:0|max:100',
        ]);

        // Ensure weights add up to 100
        if (($request->attendance_weight + $request->assessment_weight + $request->tugas_weight) != 100) {
            return redirect()->back()->withErrors(['weight_mismatch' => 'Total bobot (absensi + penilaian + tugas) harus berjumlah 100']);
        }

        $config = GradeConfiguration::firstOrNew([]);
        $config->attendance_weight = $request->attendance_weight;
        $config->assessment_weight = $request->assessment_weight;
        $config->tugas_weight = $request->tugas_weight;
        $config->grade_thresholds = [
            "A" => $request->threshold_a ?? 80,
            "A-" => $request->threshold_a_minus ?? 75,
            "B+" => $request->threshold_b_plus ?? 70,
            "B" => $request->threshold_b ?? 60,
            "B-" => $request->threshold_b_minus ?? 55,
            "C+" => $request->threshold_c_plus ?? 50,
            "C" => $request->threshold_c ?? 40,
            "C-" => $request->threshold_c_minus ?? 35,
            "D" => $request->threshold_d ?? 30,
            "E" => 0
        ];
        $config->save();

        // Redirect based on user role
        if (Auth::user()->role == 'admin') {
            return redirect()->route('admin.grades.configuration')->with('success', 'Konfigurasi nilai berhasil diperbarui');
        } elseif (Auth::user()->role == 'guru') {
            return redirect()->route('grades.configuration')->with('success', 'Konfigurasi nilai berhasil diperbarui');
        } else {
            // For any other role, redirect to grades index
            return redirect()->route('grades.index')->with('success', 'Konfigurasi nilai berhasil diperbarui');
        }
    }

    public function calculateGrades()
    {
        $students = User::where('role', 'siswa')->get();
        $results = [];

        foreach ($students as $student) {
            try {
                $result = $this->gradeCalculationService->calculateGradesForStudent($student->id);
                $results[] = $result;
            } catch (\Exception $e) {
                // Log error and continue with next student
                Log::error("Error calculating grades for student ID {$student->id}: " . $e->getMessage());
            }
        }

        // Redirect based on user role
        if (Auth::user()->role == 'admin') {
            return redirect()->route('admin.grades.index')->with('success', 'Nilai berhasil dihitung untuk semua siswa');
        } elseif (Auth::user()->role == 'guru') {
            return redirect()->route('grades.index')->with('success', 'Nilai berhasil dihitung untuk semua siswa');
        } else {
            // For any other role, redirect to grades index
            return redirect()->route('grades.index')->with('success', 'Nilai berhasil dihitung untuk semua siswa');
        }
    }

    public function getStudentGrade($studentId)
    {
        $gradeResult = GradeResult::where('user_id', $studentId)->first();
        $student = User::find($studentId);

        // Check user role to determine which view to show
        if (Auth::user()->role == 'admin') {
            return view('pages.admin.grades.student-grade', compact('gradeResult', 'student'));
        } elseif (Auth::user()->role == 'guru') {
            return view('pages.guru.grades.student-grade', compact('gradeResult', 'student'));
        } else {
            // For any other role, redirect to their grade report
            return redirect()->route('siswa.grades.report');
        }
    }

    public function getStudentGradeReport()
    {
        $studentId = Auth::user()->id;
        $gradeResult = GradeResult::where('user_id', $studentId)->first();
        $student = User::find($studentId);

        return view('pages.siswa.grades.report', compact('gradeResult', 'student'));
    }
}
