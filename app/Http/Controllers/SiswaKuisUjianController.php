<?php

namespace App\Http\Controllers;

use App\Models\KuisUjian;
use App\Models\HasilUjian;
use App\Models\JawabanSiswa;
use App\Models\Banksoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiswaKuisUjianController extends Controller
{
    public function kuis()
    {
        $kuis = KuisUjian::where('tipe', 'kuis')->get();
        return view('pages.siswa.kuis.index', compact('kuis'));
    }

    public function ujian()
    {
        $ujian = KuisUjian::where('tipe', 'ujian')->get();
        return view('pages.siswa.ujian.index', compact('ujian'));
    }

    public function showKuis($id)
    {
        $user = Auth::user();
        $kuis = KuisUjian::with('banksoals')->findOrFail($id);
        $now = Carbon::now();

        // Check if quiz has started
        if ($now->lt($kuis->waktu_mulai)) {
            return redirect()->route('siswa.kuis.index')
                ->with('error', 'Kuis belum dimulai. Mulai pada: ' . $kuis->waktu_mulai->format('d/m/Y H:i'));
        }

        // Check if quiz has ended
        if ($now->gt($kuis->waktu_selesai)) {
            return redirect()->route('siswa.kuis.index')
                ->with('error', 'Kuis sudah berakhir pada: ' . $kuis->waktu_selesai->format('d/m/Y H:i'));
        }

        // Check attempt limit for quiz
        if ($kuis->max_attempt) {
            $attemptCount = HasilUjian::where('user_id', $user->id)
                ->where('kuis_ujian_id', $id)
                ->count();
                
            if ($attemptCount >= $kuis->max_attempt) {
                return redirect()->route('siswa.kuis.index')
                    ->with('error', "Anda sudah mencapai batas maksimal {$kuis->max_attempt} kali percobaan.");
            }
        }

        // Check if student has an ongoing session
        $ongoingSession = $this->getOngoingSession($user->id, $id);
        $startTime = $ongoingSession ? $ongoingSession['start_time'] : Carbon::now();
        
        // Get existing answers if any
        $existingAnswers = JawabanSiswa::where('user_id', $user->id)
            ->where('kuis_ujian_id', $id)
            ->pluck('jawaban', 'banksoal_id')
            ->toArray();

        // Randomize questions if needed
        $banksoals = $kuis->is_random ? $kuis->banksoals->shuffle() : $kuis->banksoals;

        // Calculate remaining time
        $remainingTime = null;
        if ($kuis->durasi) {
            $elapsedMinutes = Carbon::parse($startTime)->diffInMinutes(Carbon::now());
            $remainingTime = max(0, $kuis->durasi - $elapsedMinutes);
            
            if ($remainingTime <= 0) {
                return $this->autoSubmit($user->id, $id, 'kuis');
            }
        }

        // Store session start time if new session
        if (!$ongoingSession) {
            $this->storeSessionStart($user->id, $id, $startTime);
        }

        return view('pages.siswa.kuis.show', compact(
            'kuis', 'banksoals', 'existingAnswers', 'remainingTime', 'startTime'
        ));
    }

    public function showUjian($id)
    {
        $user = Auth::user();
        $ujian = KuisUjian::with('banksoals')->findOrFail($id);
        $now = Carbon::now();

        // Check if exam has started
        if ($now->lt($ujian->waktu_mulai)) {
            return redirect()->route('siswa.ujian.index')
                ->with('error', 'Ujian belum dimulai. Mulai pada: ' . $ujian->waktu_mulai->format('d/m/Y H:i'));
        }

        // Check if exam has ended
        if ($now->gt($ujian->waktu_selesai)) {
            return redirect()->route('siswa.ujian.index')
                ->with('error', 'Ujian sudah berakhir pada: ' . $ujian->waktu_selesai->format('d/m/Y H:i'));
        }

        // Check if student already submitted
        $existingResult = HasilUjian::where('user_id', $user->id)
            ->where('kuis_ujian_id', $id)
            ->first();
            
        if ($existingResult) {
            return redirect()->route('siswa.ujian.result', $id)
                ->with('info', 'Anda sudah menyelesaikan ujian ini.');
        }

        // Check if student has an ongoing session
        $ongoingSession = $this->getOngoingSession($user->id, $id);
        $startTime = $ongoingSession ? $ongoingSession['start_time'] : Carbon::now();
        
        // Get existing answers if any
        $existingAnswers = JawabanSiswa::where('user_id', $user->id)
            ->where('kuis_ujian_id', $id)
            ->pluck('jawaban', 'banksoal_id')
            ->toArray();

        // Randomize questions if needed
        $banksoals = $ujian->is_random ? $ujian->banksoals->shuffle() : $ujian->banksoals;

        // Calculate remaining time based on exam end time
        $remainingTime = Carbon::now()->diffInMinutes($ujian->waktu_selesai, false);
        if ($remainingTime <= 0) {
            return $this->autoSubmit($user->id, $id, 'ujian');
        }

        // Store session start time if new session
        if (!$ongoingSession) {
            $this->storeSessionStart($user->id, $id, $startTime);
        }

        return view('pages.siswa.ujian.show', compact(
            'ujian', 'banksoals', 'existingAnswers', 'remainingTime', 'startTime'
        ));
    }

    public function storeJawaban(Request $request, $id)
    {
        \Log::info('Store jawaban called with ID: ' . $id);
        \Log::info('Request data: ', $request->all());
        \Log::info('Request method: ' . $request->method());
        \Log::info('CSRF token: ' . $request->header('X-CSRF-TOKEN'));
    
        $user = Auth::user();
        \Log::info('User ID: ' . $user->id);
    
        $kuisUjian = KuisUjian::with('banksoals')->findOrFail($id);
        \Log::info('Kuis Ujian found: ' . $kuisUjian->id);
    
        $validator = \Validator::make($request->all(), [
            'jawaban' => 'nullable|array',
            'jawaban.*' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            \Log::error('Validation failed: ', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        \Log::info('Validation passed');

        try {
            DB::beginTransaction();
        
            // Delete existing answers for this session
            JawabanSiswa::where('user_id', $user->id)
                ->where('kuis_ujian_id', $id)
                ->delete();

            // Store new answers with appropriate initial status
            if ($request->jawaban) {
                foreach ($request->jawaban as $banksoalId => $jawaban) {
                    if (!empty($jawaban)) {
                        // Get the banksoal to determine question type
                        $banksoal = $kuisUjian->banksoals->where('id', $banksoalId)->first();
                        
                        // Set initial status based on question type
                        $statusPenilaian = 'pending';
                        if ($banksoal && in_array($banksoal->tipe_soal, ['pilihan_ganda', 'benar_salah'])) {
                            $statusPenilaian = 'pending'; // Will be updated during scoring
                        }
                        
                        JawabanSiswa::create([
                            'user_id' => $user->id,
                            'kuis_ujian_id' => $id,
                            'banksoal_id' => $banksoalId,
                            'jawaban' => $jawaban,
                            'status_penilaian' => $statusPenilaian
                        ]);
                    }
                }
            }
        
            // Get all answers for calculation
            $answers = JawabanSiswa::where('user_id', $user->id)
                ->where('kuis_ujian_id', $id)
                ->get();
        
            \Log::info('Answers stored, count: ' . $answers->count());
        
            // Calculate and store result
            $this->calculateAndStoreResult($user->id, $id, $answers, $kuisUjian);
        
            DB::commit();
        
            $tipe = $kuisUjian->tipe;
            $routeName = $tipe === 'ujian' ? 'siswa.ujian.result' : 'siswa.kuis.index';
            $message = $tipe === 'ujian' 
                ? 'Jawaban berhasil dikumpulkan. Ujian selesai.' 
                : 'Jawaban berhasil dikumpulkan. Kuis selesai.';
        
            \Log::info('Redirecting to: ' . $routeName . ' with message: ' . $message);
        
            if ($tipe === 'ujian') {
                return redirect()->route($routeName, $id)->with('success', $message);
            } else {
                return redirect()->route($routeName)->with('success', $message);
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error storing jawaban: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Gagal menyimpan jawaban: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Helper Methods
    private function getOngoingSession($userId, $kuisUjianId)
    {
        $session = \Cache::get("exam_session_{$userId}_{$kuisUjianId}");
        return $session;
    }

    private function storeSessionStart($userId, $kuisUjianId, $startTime)
    {
        \Cache::put("exam_session_{$userId}_{$kuisUjianId}", [
            'start_time' => $startTime,
            'user_id' => $userId,
            'kuis_ujian_id' => $kuisUjianId
        ], 24 * 60); // Cache for 24 hours
    }

    private function autoSubmit($userId, $kuisUjianId, $type)
    {
        // Auto submit when time is up
        $kuisUjian = KuisUjian::with('banksoals')->findOrFail($kuisUjianId);
        $existingAnswers = JawabanSiswa::where('user_id', $userId)
            ->where('kuis_ujian_id', $kuisUjianId)
            ->get();

        $this->calculateAndStoreResult($userId, $kuisUjianId, $existingAnswers, $kuisUjian);
        
        $route = $type === 'ujian' ? 'siswa.ujian.index' : 'siswa.kuis.index';
        return redirect()->route($route)
            ->with('warning', 'Waktu habis! Jawaban Anda telah otomatis dikumpulkan.');
    }

    private function calculateAndStoreResult($userId, $kuisUjianId, $answers, $kuisUjian)
    {
        $totalQuestions = $kuisUjian->banksoals->count();
        $correctAnswers = 0;
        $wrongAnswers = 0;
        $emptyAnswers = $totalQuestions - $answers->count();
        $totalScore = 0;
        $maxScore = $kuisUjian->banksoals->sum('bobot_nilai') ?: ($totalQuestions * 10);
        $allGraded = true;

        foreach ($answers as $answer) {
            $banksoal = $kuisUjian->banksoals->where('id', $answer->banksoal_id)->first();
            if ($banksoal) {
                if (in_array($banksoal->tipe_soal, ['pilihan_ganda', 'benar_salah'])) {
                    // Auto-grade multiple choice and true/false questions
                    $isCorrect = strtolower(trim($answer->jawaban)) === strtolower(trim($banksoal->kunci_jawaban));
                    if ($isCorrect) {
                        $correctAnswers++;
                        $totalScore += ($banksoal->bobot_nilai ?? 10);
                        // Update the answer with the score and status
                        $answer->update([
                            'nilai' => ($banksoal->bobot_nilai ?? 10),
                            'status_penilaian' => 'graded'
                        ]);
                    } else {
                        $wrongAnswers++;
                        // Update the answer with zero score and status
                        $answer->update([
                            'nilai' => 0,
                            'status_penilaian' => 'graded'
                        ]);
                    }
                } else {
                    // For essay questions, mark as pending manual grading
                    $answer->update([
                        'status_penilaian' => 'pending',
                        'nilai' => null
                    ]);
                    // Essay question not yet graded
                    $allGraded = false;
                }
            }
        }

        $percentageScore = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
    
        $sessionStart = $this->getOngoingSession($userId, $kuisUjianId);
        $startTime = $sessionStart ? Carbon::parse($sessionStart['start_time']) : Carbon::now();
        $endTime = Carbon::now();
        $duration = $startTime->diffInMinutes($endTime);

        // Determine the correct status value for the hasil_ujians table
        // The table only accepts 'ongoing', 'completed', or 'expired'
        $examStatus = $allGraded ? 'completed' : 'ongoing';

        // Check if a result already exists and update it instead of creating a new one
        $existingResult = HasilUjian::where('user_id', $userId)
            ->where('kuis_ujian_id', $kuisUjianId)
            ->first();

        if ($existingResult) {
            $existingResult->update([
                'total_soal' => $totalQuestions,
                'soal_benar' => $correctAnswers,
                'soal_salah' => $wrongAnswers,
                'soal_kosong' => $emptyAnswers,
                'nilai_total' => round($percentageScore, 2),
                'grade' => $this->calculateGrade($percentageScore),
                'status' => $examStatus,
                'waktu_selesai' => $endTime,
                'durasi_pengerjaan' => $duration
            ]);
        } else {
            HasilUjian::create([
                'user_id' => $userId,
                'kuis_ujian_id' => $kuisUjianId,
                'total_soal' => $totalQuestions,
                'soal_benar' => $correctAnswers,
                'soal_salah' => $wrongAnswers,
                'soal_kosong' => $emptyAnswers,
                'nilai_total' => round($percentageScore, 2),
                'grade' => $this->calculateGrade($percentageScore),
                'status' => $examStatus,
                'waktu_mulai' => $startTime,
                'waktu_selesai' => $endTime,
                'durasi_pengerjaan' => $duration,
                'attempt_number' => HasilUjian::where('user_id', $userId)
                    ->where('kuis_ujian_id', $kuisUjianId)
                    ->count() + 1
            ]);
        }

        // Clear session cache
        \Cache::forget("exam_session_{$userId}_{$kuisUjianId}");
    }

    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    // Auto-save answers (AJAX endpoint)
    public function autoSaveAnswer(Request $request, $id)
    {
        $request->validate([
            'banksoal_id' => 'required|exists:banksoals,id',
            'jawaban' => 'nullable|string'
        ]);

        $userId = Auth::id();
        $kuisUjian = KuisUjian::with('banksoals')->findOrFail($id);
        
        // Get the banksoal to determine question type
        $banksoal = $kuisUjian->banksoals->where('id', $request->banksoal_id)->first();
        
        // Set initial status based on question type
        $statusPenilaian = 'pending';
        if ($banksoal && in_array($banksoal->tipe_soal, ['pilihan_ganda', 'benar_salah'])) {
            $statusPenilaian = 'pending'; // Will be updated during scoring
        }
        
        JawabanSiswa::updateOrCreate(
            [
                'user_id' => $userId,
                'kuis_ujian_id' => $id,
                'banksoal_id' => $request->banksoal_id
            ],
            [
                'jawaban' => $request->jawaban ?? '',
                'status_penilaian' => $statusPenilaian
            ]
        );

        return response()->json(['success' => true, 'message' => 'Answer auto-saved']);
    }

    // Get exam result
    public function examResult($id)
    {
        $user = Auth::user();
        $result = HasilUjian::with(['kuisUjian.mapel', 'kuisUjian.guru'])
            ->where('user_id', $user->id)
            ->where('kuis_ujian_id', $id)
            ->latest()
            ->first();

        if (!$result) {
            return redirect()->route('siswa.ujian.index')
                ->with('error', 'Hasil ujian tidak ditemukan.');
        }

        // Get detailed answer information including manual scores
        $kuisUjian = KuisUjian::with([
            'banksoals',
            'jawabanSiswas' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }
        ])->findOrFail($id);

        $jawabanDetails = $kuisUjian->jawabanSiswas->keyBy('banksoal_id');

        $detailedAnalysis = $result->getDetailedAnalysis();
        $classComparison = $result->compareWithClassAverage();

        return view('pages.siswa.ujian.result', compact('result', 'detailedAnalysis', 'classComparison', 'kuisUjian', 'jawabanDetails'));
    }
}