<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KuisUjian;
use App\Models\Banksoal;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\JawabanSiswa;
use App\Services\ExcelImportService;
use App\Exports\QuestionTemplateExport;
use App\Models\Guru;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;



class KuisUjianController extends Controller
{
    public function index()
    {
        $guru = Guru::where('user_id', Auth::id())->first();

        if (!$guru) {
            return back()->with('error', 'Data guru tidak ditemukan.');
        }

        $kuisUjians = KuisUjian::with('mapel')
            ->where('guru_id', $guru->id)
            ->get();

        return view('pages.guru.kuis_ujian.index', compact('kuisUjians'));
    }



    public function create()
    {
        $mapels = Mapel::all();
        $guru = Guru::where('user_id', Auth::id())->first();
        $banksoals = Banksoal::where('guru_id', $guru->id)->get();
        return view('pages.guru.kuis_ujian.create', compact('mapels', 'banksoals'));
    }

    public function store(Request $request)
    {
        $guru = Guru::where('user_id', Auth::id())->first();
        if (!$guru) {
            return back()->with('error', 'Data guru tidak ditemukan.');
        }


        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'mapel_id' => 'required|exists:mapels,id',
            'tipe' => 'required|in:kuis,ujian',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'banksoals' => 'required|array|min:1',
            'banksoals.*' => 'exists:banksoals,id',
            'max_attempt' => 'nullable|integer|min:1',
            'is_random' => 'required|boolean'
        ]);

        // Hitung durasi otomatis
        // $start = strtotime($request->waktu_mulai);
        // $end = strtotime($request->waktu_selesai);
        // $durasi = ($end - $start) / 60;
        $durasi = Carbon::parse($request->waktu_mulai)
            ->diffInMinutes(Carbon::parse($request->waktu_selesai));

        // Simpan data kuis/ujian
        $kuis = KuisUjian::create([
            'guru_id' => $guru->id,
            'judul' => $request->judul,
            'mapel_id' => $request->mapel_id,
            'tipe' => $request->tipe,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'durasi' => $request->tipe === 'kuis' ? $durasi : null,
            'max_attempt' => $request->tipe === 'kuis' ? $request->max_attempt : null,
            'is_random' => $request->is_random,
        ]);

        // Simpan relasi many-to-many ke tabel pivot
        if ($kuis) {
            $kuis->banksoals()->attach($request->banksoals);

            Log::info('Kuis berhasil disimpan:', $kuis->toArray());
            return redirect()->route('kuis_ujian.index')->with('success', 'Kuis berhasil dibuat.');
        } else {
            Log::warning('Gagal menyimpan kuis.');
            return back()->with('error', 'Gagal menyimpan kuis.');
        }
    }

    public function edit($id)
    {
        $kuisUjian = KuisUjian::findOrFail($id);
        $mapels = Mapel::all();
        $banksoals = Banksoal::all();

        // Ambil soal-soal yang terkait via pivot
        $selectedSoalIds = $kuisUjian->banksoals->pluck('id')->toArray();

        return view('pages.guru.kuis_ujian.edit', compact('kuisUjian', 'mapels', 'banksoals', 'selectedSoalIds'));
    }



    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'mapel_id' => 'required|exists:mapels,id',
            'tipe' => 'required|in:kuis,ujian',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'banksoals' => 'required|array|min:1',
            'banksoals.*' => 'exists:banksoals,id',
            'max_attempt' => 'nullable|integer|min:1',
            'is_random' => 'required|boolean'
        ]);

        $kuis = KuisUjian::findOrFail($id);

        $durasi = null;
        if ($request->tipe === 'kuis') {
            $mulai = Carbon::parse($request->waktu_mulai);
            $selesai = Carbon::parse($request->waktu_selesai);
            $durasi = $mulai->diffInMinutes($selesai);
        }

        $kuis->update([
            'judul' => $request->judul,
            'mapel_id' => $request->mapel_id,
            'tipe' => $request->tipe,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'durasi' => $request->tipe === 'kuis' ? $durasi : null,
            'max_attempt' => $request->tipe === 'kuis' ? $request->max_attempt : null,
            'is_random' => $request->is_random,
        ]);

        $kuis->banksoals()->sync($request->banksoals); // Ganti isi tabel pivot-nya

        return redirect()->route('kuis_ujian.index')->with('success', 'Kuis berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kuisUjian = KuisUjian::findOrFail($id);
        $kuisUjian->banksoals()->detach();
        $kuisUjian->delete();
        // $kuis = KuisUjian::findOrFail($id);

        // KuisUjian::where('judul', $kuis->judul)
        //     ->where('tipe', $kuis->tipe)
        //     ->where('guru_id', $kuis->guru_id)
        //     ->delete();

        return redirect()->route('kuis_ujian.index')->with('success', 'Kuis/Ujian berhasil dihapus.');
    }

    public function hasil($id)
    {
        $kuis = KuisUjian::with(['jawabanSiswas.user', 'jawabanSiswas.banksoal'])->findOrFail($id);
        $siswa = $kuis->jawabanSiswas->groupBy('user_id');

        return view('pages.guru.kuis_ujian.hasil', compact('kuis', 'siswa'));
    }

    public function lihatJawabanSiswa($id, $userId)
    {
        $kuis = KuisUjian::with([
            'banksoals',
            'jawabanSiswas' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }
        ])->findOrFail($id);

        $user = \App\Models\User::findOrFail($userId);
        $jawabanSiswa = $kuis->jawabanSiswas->keyBy('banksoal_id');

        // dd($kuis->toArray());

        return view('pages.guru.kuis_ujian.lihat_jawaban', compact('kuis', 'user', 'jawabanSiswa'));
    }

    // Manual scoring methods
    public function showManualScoring($id, $userId)
    {
        $kuis = KuisUjian::with([
            'banksoals' => function ($query) {
                $query->whereIn('tipe_soal', ['essay', 'esai']);
            },
            'jawabanSiswas' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }
        ])->findOrFail($id);

        // Only get answers that need manual grading
        $jawabanSiswa = $kuis->jawabanSiswas->filter(function ($jawaban) {
            $banksoal = $jawaban->banksoal;
            return $banksoal && in_array($banksoal->tipe_soal, ['essay', 'esai']);
        })->keyBy('banksoal_id');

        $user = \App\Models\User::findOrFail($userId);

        return view('pages.guru.kuis_ujian.manual_scoring', compact('kuis', 'user', 'jawabanSiswa'));
    }

    public function storeManualScores(Request $request, $id, $userId)
    {
        // Validate the request
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*.banksoal_id' => 'required|exists:banksoals,id',
            'scores.*.nilai' => 'required|numeric|min:0|max:100',
            'scores.*.feedback' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $totalScore = 0;
            $updatedScores = [];
            $kuis = KuisUjian::findOrFail($id);

            // Validate that the exam belongs to the current teacher
            $guru = Guru::where('user_id', Auth::id())->first();
            if ($kuis->guru_id != $guru->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this exam.'
                ], 403);
            }

            // Validate that the user has answered this exam
            $userAnswers = JawabanSiswa::where('kuis_ujian_id', $id)
                ->where('user_id', $userId)
                ->count();

            if ($userAnswers == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This student has not answered this exam.'
                ], 400);
            }

            foreach ($request->scores as $score) {
                // Validate that the question belongs to this exam
                $banksoalExists = $kuis->banksoals()->where('banksoal_id', $score['banksoal_id'])->exists();
                if (!$banksoalExists) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid question ID: ' . $score['banksoal_id']
                    ], 400);
                }

                $jawabanSiswa = JawabanSiswa::where('kuis_ujian_id', $id)
                    ->where('user_id', $userId)
                    ->where('banksoal_id', $score['banksoal_id'])
                    ->first();

                if ($jawabanSiswa) {
                    $jawabanSiswa->update([
                        'nilai' => $score['nilai'],
                        'status_penilaian' => 'graded',
                        'feedback' => $score['feedback'] ?? null
                    ]);

                    $totalScore += $score['nilai'];
                    $updatedScores[] = [
                        'banksoal_id' => $score['banksoal_id'],
                        'nilai' => $score['nilai'],
                        'status_penilaian' => 'graded'
                    ];
                }
            }

            // Update the overall exam result with manual scores
            $this->updateExamResultWithManualScores($id, $userId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Scores updated successfully',
                'updated_scores' => $updatedScores
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in manual scoring: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update scores. Please try again.'
            ], 500);
        }
    }

    private function updateExamResultWithManualScores($kuisUjianId, $userId)
    {
        // Get the exam
        $kuisUjian = KuisUjian::findOrFail($kuisUjianId);

        // Get the exam result
        $hasilUjian = \App\Models\HasilUjian::where('kuis_ujian_id', $kuisUjianId)
            ->where('user_id', $userId)
            ->first();

        if (!$hasilUjian) {
            return;
        }

        // Get all answers for this exam
        $allAnswers = JawabanSiswa::where('kuis_ujian_id', $kuisUjianId)
            ->where('user_id', $userId)
            ->with('banksoal')
            ->get();

        // Calculate new total score including manual scores
        $totalScore = 0;
        $maxScore = 0;
        $correctAnswers = 0;
        $wrongAnswers = 0;
        $emptyAnswers = 0;
        $allGraded = true;

        foreach ($allAnswers as $answer) {
            $banksoal = $answer->banksoal;
            if (!$banksoal) continue;

            $maxScore += ($banksoal->bobot_nilai ?? 10);

            if (in_array($banksoal->tipe_soal, ['pilihan_ganda', 'benar_salah'])) {
                // Auto-graded questions
                $isCorrect = strtolower(trim($answer->jawaban)) === strtolower(trim($banksoal->kunci_jawaban));
                if ($isCorrect) {
                    $correctAnswers++;
                    $totalScore += ($banksoal->bobot_nilai ?? 10);
                } else {
                    $wrongAnswers++;
                }
            } else {
                // Manually graded questions
                if ($answer->status_penilaian === 'graded' && $answer->nilai !== null) {
                    $totalScore += $answer->nilai;
                    $correctAnswers++; // Count as correct for statistics
                } else {
                    $allGraded = false; // Not all questions have been graded
                }
            }
        }

        // Update the exam result
        $percentageScore = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

        $hasilUjian->update([
            'soal_benar' => $correctAnswers,
            'soal_salah' => $wrongAnswers,
            'soal_kosong' => $emptyAnswers,
            'nilai_total' => round($percentageScore, 2),
            'grade' => $this->calculateGrade($percentageScore),
            'status' => $allGraded ? 'completed' : 'pending_manual_grading'
        ]);
    }

    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    // Excel Import Methods
    public function importQuestions()
    {
        $mapels = Mapel::all();
        return view('pages.guru.kuis_ujian.import-questions', compact('mapels'));
    }

    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'mapel_id' => 'required|exists:mapels,id'
        ]);

        $importService = new ExcelImportService();

        // Validate file first
        $fileErrors = $importService->validateFile($request->file('file'));
        if (!empty($fileErrors)) {
            return redirect()->back()
                ->with('error', implode(', ', $fileErrors))
                ->withInput();
        }

        $result = $importService->importQuestions(
            $request->file('file'),
            $request->mapel_id,
            true // preview only
        );

        $mapel = Mapel::find($request->mapel_id);

        return view('pages.guru.kuis_ujian.preview-import', compact('result', 'mapel'));
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'mapel_id' => 'required|exists:mapels,id'
        ]);

        $importService = new ExcelImportService();

        $result = $importService->importQuestions(
            $request->file('file'),
            $request->mapel_id,
            false // actual import
        );

        if ($result['success']) {
            return redirect()->route('banksoal.index')
                ->with('success', "Berhasil mengimpor {$result['imported_count']} soal dari Excel");
        } else {
            return redirect()->back()
                ->with('error', 'Gagal mengimpor soal: ' . implode(', ', $result['errors']))
                ->withInput();
        }
    }

    public function downloadTemplate()
    {
        $importService = new ExcelImportService();
        $template = $importService->generateTemplate();

        return Excel::download(new QuestionTemplateExport($template), 'template_soal.xlsx');
    }

    public function bulkCreateFromImport(Request $request)
    {
        $guru = Guru::where('user_id', Auth::id())->first();
        $request->validate([
            'questions' => 'required|array',
            'mapel_id' => 'required|exists:mapels,id',
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:kuis,ujian',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'max_attempt' => 'nullable|integer|min:1',
            'is_random' => 'required|boolean'
        ]);

        try {
            DB::beginTransaction();

            // Create questions first
            $questionIds = [];
            foreach ($request->questions as $questionData) {
                $question = Banksoal::create(array_merge($questionData, [
                    'guru_id' => $guru->id,
                    'mapel_id' => $request->mapel_id
                ]));
                $questionIds[] = $question->id;
            }

            // Calculate duration
            $durasi = Carbon::parse($request->waktu_mulai)
                ->diffInMinutes(Carbon::parse($request->waktu_selesai));

            // Create exam/quiz
            $kuis = KuisUjian::create([
                'guru_id' => $guru->id,
                'judul' => $request->judul,
                'mapel_id' => $request->mapel_id,
                'tipe' => $request->tipe,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'durasi' => $request->tipe === 'kuis' ? $durasi : null,
                'max_attempt' => $request->tipe === 'kuis' ? $request->max_attempt : null,
                'is_random' => $request->is_random,
            ]);

            // Attach questions to exam
            $kuis->banksoals()->attach($questionIds);

            DB::commit();

            return redirect()->route('kuis_ujian.index')
                ->with('success', 'Kuis/Ujian berhasil dibuat dengan ' . count($questionIds) . ' soal dari import');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating exam from import: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal membuat kuis/ujian: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Helper method to check if manual scoring is required for a quiz/exam
    private function requiresManualScoring($kuisUjianId, $userId)
    {
        $kuis = KuisUjian::with(['banksoals', 'jawabanSiswas' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->findOrFail($kuisUjianId);

        // Check if there are any essay questions that haven't been graded
        foreach ($kuis->jawabanSiswas as $jawaban) {
            $banksoal = $jawaban->banksoal;
            if (
                $banksoal && in_array($banksoal->tipe_soal, ['essay', 'esai']) &&
                ($jawaban->status_penilaian !== 'graded' || $jawaban->nilai === null)
            ) {
                return true;
            }
        }

        return false;
    }
}
