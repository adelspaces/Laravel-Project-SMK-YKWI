<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\GuruSuperController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PengumumanSekolahController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\BanksoalController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KuisUjianController;
use App\Http\Controllers\SiswaKuisUjianController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MasterAbsensiController;
use App\Http\Controllers\AbsensiSiswaController;
use App\Http\Controllers\GradeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root route - redirect to appropriate dashboard based on user role
Route::get('/', function () {
    if (Auth::check()) {
        // If user is authenticated, redirect to their dashboard
        $user = Auth::user();
        switch ($user->role) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'guru':
                return redirect('/guru/dashboard');
            case 'siswa':
                return redirect('/siswa/dashboard');
            default:
                return redirect('/home');
        }
    }
    // If not authenticated, show login page
    return view('auth.login');
});

Auth::routes();

// Dashboard & Profile
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Profile
    Route::get('/profile', [UserController::class, 'edit'])->name('profile');
    Route::put('/update-profile', [UserController::class, 'update'])->name('update.profile');
    Route::get('/edit-password', [UserController::class, 'editPassword'])->name('ubah-password');
    Route::patch('/update-password', [UserController::class, 'updatePassword'])->name('update-password');
});

// Role: Guru
Route::middleware(['auth', 'checkRole:guru'])->group(function () {
    // Dashboard Guru
    Route::get('/guru/dashboard', [HomeController::class, 'guru'])->name('guru.dashboard');
    Route::get('/guru/dashboard', [GuruController::class, 'dashboard'])->name('guru.dashboard');

    // Test SweetAlert
    Route::get('/test-sweetalert', function () {
        return view('test-sweetalert');
    });

    // Materi & Tugas
    Route::resource('materi', MateriController::class);
    Route::resource('tugas', TugasController::class);

    // Bank Soal Excel Import (must be before resource routes)
    Route::get('/banksoal/import-questions', [BanksoalController::class, 'importQuestions'])->name('banksoal.import_questions');
    Route::post('/banksoal/preview-import', [BanksoalController::class, 'previewImport'])->name('banksoal.preview_import');
    Route::post('/banksoal/process-import', [BanksoalController::class, 'processImport'])->name('banksoal.process_import');
    Route::post('/banksoal/confirm-import', [BanksoalController::class, 'confirmImport'])->name('banksoal.confirm_import');
    Route::get('/banksoal/cancel-import', [BanksoalController::class, 'cancelImport'])->name('banksoal.cancel_import');
    Route::get('/banksoal/download-template', [BanksoalController::class, 'downloadTemplate'])->name('banksoal.download_template');

    // Bank Soal
    Route::resource('banksoal', BanksoalController::class);

    // Siswa & Jawaban Tugas
    Route::get('/guru/siswa', [GuruController::class, 'siswaSaya'])->name('guru.siswa');
    Route::get('/guru/jawaban-download/{id}', [TugasController::class, 'downloadJawaban'])->name('guru.jawaban.download');

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/absensi/riwayat', [AbsensiController::class, 'riwayat'])->name('absensi.riwayat');
    Route::get('/absensi/bulk-entry', [AbsensiController::class, 'bulkEntry'])->name('absensi.bulk-entry');
    Route::get('/absensi/students-by-class', [AbsensiController::class, 'getStudentsByClass'])->name('absensi.students-by-class');
    Route::get('/absensi/export', [AbsensiController::class, 'exportExcel'])->name('absensi.export');
    Route::get('/absensi/statistics', [AbsensiController::class, 'statistics'])->name('absensi.statistics');

    // Master Absensi
    Route::get('/absensi/create-master', [MasterAbsensiController::class, 'create'])->name('absensi.master.create');
    Route::post('/absensi', [MasterAbsensiController::class, 'store'])->name('absensi.master.store');
    Route::get('/absensi/{id}/student-attendance', [MasterAbsensiController::class, 'showStudentAttendance'])->name('absensi.master.show-student-attendance');
    Route::get('/absensi/{id}/edit-student-attendance', [MasterAbsensiController::class, 'editStudentAttendance'])->name('absensi.master.edit-student-attendance');
    Route::put('/absensi/{id}/update-student-attendance', [MasterAbsensiController::class, 'updateStudentAttendance'])->name('absensi.master.update-student-attendance');
    Route::delete('/absensi/{id}', [MasterAbsensiController::class, 'destroy'])->name('absensi.master.destroy');

    Route::resource('absensi', AbsensiController::class)->except(['index', 'store']);

    // Kuis & Ujian
    Route::get('/guru/kuis-ujian/{id}/hasil', [KuisUjianController::class, 'hasil'])
        ->name('guru.kuis_ujian.hasil');
    Route::get('/guru/kuis-ujian/{id}/siswa/{siswaId}', [KuisUjianController::class, 'lihatJawabanSiswa'])
        ->name('guru.kuis_ujian.lihatJawaban');
    Route::get('/guru/kuis-ujian/{id}/siswa/{siswaId}/manual-scoring', [KuisUjianController::class, 'showManualScoring'])
        ->name('guru.kuis_ujian.manual_scoring');
    Route::post('/guru/kuis-ujian/{id}/siswa/{siswaId}/manual-scores', [KuisUjianController::class, 'storeManualScores'])
        ->name('guru.kuis_ujian.store_manual_scores');

    // Excel Import for Questions
    Route::get('/kuis-ujian/import-questions', [KuisUjianController::class, 'importQuestions'])->name('kuis_ujian.import_questions');
    Route::post('/kuis-ujian/preview-import', [KuisUjianController::class, 'previewImport'])->name('kuis_ujian.preview_import');
    Route::post('/kuis-ujian/process-import', [KuisUjianController::class, 'processImport'])->name('kuis_ujian.process_import');
    Route::get('/kuis-ujian/download-template', [KuisUjianController::class, 'downloadTemplate'])->name('kuis_ujian.download_template');
    Route::post('/kuis-ujian/bulk-create-from-import', [KuisUjianController::class, 'bulkCreateFromImport'])->name('kuis_ujian.bulk_create_from_import');

    Route::resource('kuis-ujians', KuisUjianController::class)->names([
        'index' => 'kuis_ujian.index',
        'create' => 'kuis_ujian.create',
        'store' => 'kuis_ujian.store',
        'edit' => 'kuis_ujian.edit',
        'update' => 'kuis_ujian.update',
        'destroy' => 'kuis_ujian.destroy',
    ]);
    Route::put('/guru/kuis_ujian/{id}', [KuisUjianController::class, 'update'])->name('pages.guru.kuis-ujian.update');

    // Reports for Teachers
    Route::get('/reports/teacher', [ReportController::class, 'teacherReport'])->name('reports.teacher');
    Route::get('/reports/class', [ReportController::class, 'classReport'])->name('reports.class');
    Route::get('/reports/class/export', [ReportController::class, 'exportClassReport'])->name('reports.class.export');

    // Grade Management
    Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
    Route::get('/grades/configuration', [GradeController::class, 'showConfiguration'])->name('grades.configuration');
    Route::post('/grades/configuration', [GradeController::class, 'updateConfiguration'])->name('grades.updateConfiguration');
    Route::post('/grades/calculate', [GradeController::class, 'calculateGrades'])->name('grades.calculate');
    Route::get('/grades/student/{id}', [GradeController::class, 'getStudentGrade'])->name('grades.student');
    Route::get('/grades/export/all', [GradeController::class, 'exportAll'])->name('grades.exportAll');
    Route::get('/grades/export/student/{id}', [GradeController::class, 'exportStudent'])->name('grades.exportStudent');
    Route::get('/grades/export/all/pdf', [GradeController::class, 'exportAllPdf'])->name('grades.exportAllPdf');
    Route::get('/grades/export/student/{id}/pdf', [GradeController::class, 'exportStudentPdf'])->name('grades.exportStudentPdf');

    Route::post('/tugas/jawaban/{jawaban}/nilai', [TugasController::class, 'beriNilai'])
        ->name('tugas.jawaban.nilai');

    Route::get('/tugas/{tugas}/rekap', [TugasController::class, 'rekap'])
        ->name('tugas.rekap');
    Route::get('/tugas/{tugas}/rekap/export', [TugasController::class, 'exportRekap'])
        ->name('tugas.rekap.export');
});


// Role: Siswa
Route::middleware(['auth', 'checkRole:siswa'])->group(function () {
    // Dashboard & Materi
    Route::get('/siswa/dashboard', [HomeController::class, 'siswa'])->name('siswa.dashboard');
    Route::get('/siswa/materi', [MateriController::class, 'siswa'])->name('siswa.materi');
    Route::get('/materi-download/{id}', [MateriController::class, 'download'])->name('siswa.materi.download');

    // Tugas
    Route::get('/siswa/tugas', [TugasController::class, 'siswa'])->name('siswa.tugas');
    Route::get('/tugas-download/{id}', [TugasController::class, 'download'])->name('siswa.tugas.download');
    Route::post('/siswa/tugas/kirim', [TugasController::class, 'kirimJawaban'])->name('kirim-jawaban');

    // Kuis
    Route::get('/siswa/kuis', [SiswaKuisUjianController::class, 'kuis'])->name('siswa.kuis.index');
    Route::get('/siswa/kuis/{id}', [SiswaKuisUjianController::class, 'showKuis'])->name('siswa.kuis.show');
    Route::post('/siswa/kuis/{id}/store-jawaban', [SiswaKuisUjianController::class, 'storeJawaban'])->name('siswa.kuis.storeJawaban');
    Route::post('/siswa/kuis/{id}/submit', [SiswaKuisUjianController::class, 'submitKuis'])->name('siswa.kuis.submit');
    Route::post('/siswa/kuis/{id}/auto-save', [SiswaKuisUjianController::class, 'autoSaveAnswer'])->name('siswa.kuis.auto_save');

    // Ujian
    Route::get('/siswa/ujian', [SiswaKuisUjianController::class, 'ujian'])->name('siswa.ujian.index');
    Route::get('/siswa/ujian/{id}', [SiswaKuisUjianController::class, 'showUjian'])->name('siswa.ujian.show');
    Route::post('/siswa/ujian/{id}/store-jawaban', [SiswaKuisUjianController::class, 'storeJawaban'])->name('siswa.ujian.storeJawaban');
    Route::post('/siswa/ujian/{id}/submit', [SiswaKuisUjianController::class, 'submitUjian'])->name('siswa.ujian.submit');
    Route::post('/siswa/ujian/{id}/auto-save', [SiswaKuisUjianController::class, 'autoSaveAnswer'])->name('siswa.ujian.auto_save');
    Route::get('/siswa/ujian/{id}/result', [SiswaKuisUjianController::class, 'examResult'])->name('siswa.ujian.result');

    // Student Reports
    Route::get('/siswa/reports', [ReportController::class, 'studentReport'])->name('siswa.reports');
    Route::get('/siswa/reports/export', [ReportController::class, 'exportStudentReport'])->name('siswa.reports.export');

    // Student Grades
    Route::get('/siswa/grades/report', [GradeController::class, 'getStudentGradeReport'])->name('siswa.grades.report');

    // Absensi Siswa (Two-table system)
    Route::get('/siswa/absensi', [AbsensiSiswaController::class, 'index'])->name('absensi.siswa.index');
    Route::get('/siswa/absensi/statistics', [AbsensiSiswaController::class, 'statistics'])->name('absensi.siswa.statistics');
    Route::post('/siswa/absensi/siswa/{id}/submit', [AbsensiSiswaController::class, 'submitAttendance'])->name('absensi.siswa.submit');
});


// Role: Admin
Route::middleware(['auth', 'checkRole:admin'])->group(function () {
    Route::get('/admin/dashboard', [HomeController::class, 'admin'])->name('admin.dashboard');
    Route::resource('jurusan', JurusanController::class);
    Route::resource('mapel', MapelController::class);
    Route::resource('guru', GuruController::class);
    Route::resource('kelas', KelasController::class);
    Route::resource('siswa', SiswaController::class);
    Route::resource('user', UserController::class);
    Route::resource('jadwal', JadwalController::class);
    Route::resource('pengumuman-sekolah', PengumumanSekolahController::class);
    Route::resource('pengaturan', PengaturanController::class);

    // Register Guru
    Route::get('/register/guru', [App\Http\Controllers\Auth\RegisterGuruController::class, 'showRegistrationForm'])->name('register.guru');
    Route::post('/register/guru', [App\Http\Controllers\Auth\RegisterGuruController::class, 'register']);

    // Register Siswa
    Route::get('/register/siswa', [App\Http\Controllers\Auth\RegisterSiswaController::class, 'showRegistrationForm'])->name('register.siswa');
    Route::post('/register/siswa', [App\Http\Controllers\Auth\RegisterSiswaController::class, 'register']);

    // Comprehensive Reports for Admin
    Route::get('/admin/reports', [ReportController::class, 'studentReport'])->name('admin.reports');
    Route::get('/admin/reports/student/{id}', [ReportController::class, 'studentReport'])->name('admin.reports.student');
    Route::get('/admin/reports/class', [ReportController::class, 'classReport'])->name('admin.reports.class');
    Route::get('/admin/reports/export/student/{id}', [ReportController::class, 'exportStudentReport'])->name('admin.reports.export.student');
    Route::get('/admin/reports/export/class', [ReportController::class, 'exportClassReport'])->name('admin.reports.export.class');

    // Grade Management
    Route::get('/admin/grades', [GradeController::class, 'index'])->name('admin.grades.index');
    Route::get('/admin/grades/configuration', [GradeController::class, 'showConfiguration'])->name('admin.grades.configuration');
    Route::post('/admin/grades/configuration', [GradeController::class, 'updateConfiguration'])->name('admin.grades.updateConfiguration');
    Route::post('/admin/grades/calculate', [GradeController::class, 'calculateGrades'])->name('admin.grades.calculate');
    Route::get('/admin/grades/student/{id}', [GradeController::class, 'getStudentGrade'])->name('admin.grades.student');
    Route::get('/admin/grades/export/all', [GradeController::class, 'exportAll'])->name('admin.grades.exportAll');
    Route::get('/admin/grades/export/student/{id}', [GradeController::class, 'exportStudent'])->name('admin.grades.exportStudent');
    Route::get('/admin/grades/export/all/pdf', [GradeController::class, 'exportAllPdf'])->name('admin.grades.exportAllPdf');
    Route::get('/admin/grades/export/student/{id}/pdf', [GradeController::class, 'exportStudentPdf'])->name('admin.grades.exportStudentPdf');
});
