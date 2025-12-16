<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\KuisUjianController;
use App\Http\Controllers\SiswaKuisUjianController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes for AJAX functionality
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Attendance API
    Route::prefix('attendance')->group(function () {
        Route::get('/classes/{id}/students', [AbsensiController::class, 'getStudentsByClass']);
        Route::post('/bulk', [AbsensiController::class, 'store']);
        Route::get('/history/{studentId}', function($studentId) {
            return \App\Models\Absensi::where('siswa_id', $studentId)
                ->with(['kelas', 'mapel', 'guru'])
                ->orderBy('tanggal', 'desc')
                ->paginate(20);
        });
        Route::get('/stats/{classId}', function($classId) {
            $stats = \App\Models\Absensi::where('kelas_id', $classId)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();
            return response()->json($stats);
        });
        Route::get('/export/{classId}', [AbsensiController::class, 'exportExcel']);
    });
    
    // Exam Management API
    Route::prefix('exams')->group(function () {
        Route::post('/import', [KuisUjianController::class, 'processImport']);
        Route::post('/preview', [KuisUjianController::class, 'previewImport']);
        Route::get('/{id}/questions', function($id) {
            return \App\Models\KuisUjian::with('banksoals')->findOrFail($id);
        });
        Route::post('/{id}/submit', [SiswaKuisUjianController::class, 'storeJawaban']);
        Route::get('/{id}/results', function($id) {
            return \App\Models\HasilUjian::where('kuis_ujian_id', $id)
                ->with(['user', 'kuisUjian'])
                ->get();
        });
        Route::post('/{id}/auto-save', [SiswaKuisUjianController::class, 'autoSaveAnswer']);
    });
    
    // Reports API
    Route::prefix('reports')->group(function () {
        Route::get('/student/{id}', function($id) {
            $reportController = new ReportController();
            return $reportController->studentReport(request(), $id);
        });
        Route::get('/class/{id}', function($id) {
            $reportController = new ReportController();
            return $reportController->classReport(request());
        });
        Route::get('/subject/{id}', function($id) {
            return \App\Models\HasilUjian::whereHas('kuisUjian', function($q) use ($id) {
                $q->where('mapel_id', $id);
            })->with(['user', 'kuisUjian'])->get();
        });
        Route::get('/attendance/{id}', function($id) {
            return \App\Models\Absensi::where('siswa_id', $id)
                ->with(['kelas', 'mapel', 'guru'])
                ->orderBy('tanggal', 'desc')
                ->get();
        });
        Route::post('/custom', function(Request $request) {
            // Implementation for custom report generation
            return response()->json(['message' => 'Custom report endpoint']);
        });
    });
    
    // Real-time exam monitoring
    Route::prefix('monitoring')->group(function () {
        Route::get('/exam/{id}/active-students', function($id) {
            return \App\Models\JawabanSiswa::where('kuis_ujian_id', $id)
                ->with('user')
                ->selectRaw('user_id, MAX(updated_at) as last_activity')
                ->groupBy('user_id')
                ->get();
        });
        Route::get('/exam/{id}/progress', function($id) {
            $totalQuestions = \App\Models\KuisUjian::find($id)->banksoals()->count();
            $progress = \App\Models\JawabanSiswa::where('kuis_ujian_id', $id)
                ->selectRaw('user_id, COUNT(*) as answered_questions')
                ->groupBy('user_id')
                ->with('user')
                ->get()
                ->map(function($item) use ($totalQuestions) {
                    $item->progress_percentage = ($item->answered_questions / $totalQuestions) * 100;
                    return $item;
                });
            return response()->json($progress);
        });
    });
});
