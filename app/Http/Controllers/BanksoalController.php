<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banksoal;
use App\Models\Mapel;
use App\Models\Ujian;
use App\Models\Guru;
use App\Services\ExcelImportService;
use App\Exports\QuestionTemplateExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class BanksoalController extends Controller
{
    public function index()
    {
        $guru = Guru::where('user_id', Auth::id())->first();
        $banksoal = Banksoal::where('guru_id', $guru->id)->get();

        return view('pages.guru.banksoal.index', compact('banksoal'));
    }


    public function create()
    {
        $mapels = Mapel::all();
        // $ujians = Ujian::all();
        return view('pages.guru.banksoal.create', compact('mapels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mapel_id' => 'required|exists:mapels,id',
            'tipe_soal' => 'required|in:pilihan_ganda,esai,benar_salah',
            'pertanyaan' => 'required|string',
            'opsi_a' => 'nullable|string',
            'opsi_b' => 'nullable|string',
            'opsi_c' => 'nullable|string',
            'opsi_d' => 'nullable|string',
            'jawaban_benar' => 'nullable|string',
            'kunci_jawaban' => 'nullable|string',
        ]);

        $guru = Guru::where('user_id', Auth::id())->first();

        if (!$guru) {
            return back()->with('error', 'Data guru tidak ditemukan.');
        }

        $data = [
            'mapel_id' => $request->mapel_id,
            'guru_id' => $guru->id,
            'pertanyaan' => $request->pertanyaan,
            'tipe_soal' => $request->tipe_soal,
            'bobot_nilai' => $request->bobot_nilai ?? 10,
            'tingkat_kesulitan' => $request->tingkat_kesulitan ?? 'sedang',
        ];

        if ($request->tipe_soal === 'pilihan_ganda') {
            $data['opsi_a'] = $request->opsi_a;
            $data['opsi_b'] = $request->opsi_b;
            $data['opsi_c'] = $request->opsi_c;
            $data['opsi_d'] = $request->opsi_d;
            $data['opsi_e'] = $request->opsi_e;
            $data['jawaban_benar'] = strtoupper($request->jawaban_benar);
            $data['kunci_jawaban'] = null;
        } elseif ($request->tipe_soal === 'benar_salah') {
            $data['kunci_jawaban'] = $request->kunci_jawaban;
            $data['opsi_a'] = null;
            $data['opsi_b'] = null;
            $data['opsi_c'] = null;
            $data['opsi_d'] = null;
            $data['opsi_e'] = null;
            $data['jawaban_benar'] = strtoupper($request->jawaban_benar);
        } else {
            $data['kunci_jawaban'] = $request->kunci_jawaban;
            $data['opsi_a'] = null;
            $data['opsi_b'] = null;
            $data['opsi_c'] = null;
            $data['opsi_d'] = null;
            $data['opsi_e'] = null;
            $data['jawaban_benar'] = null;
        }

        Banksoal::create($data);

        return redirect()->route('banksoal.index')->with('success', 'Soal berhasil ditambahkan.');
    }


    public function show($id)
    {
        $soal = Banksoal::findOrFail($id);
        return view('pages.guru.banksoal.show', compact('soal'));
    }


    public function edit($id)
    {
        $soal = Banksoal::findOrFail($id);
        $mapels = Mapel::all();
        return view('pages.guru.banksoal.edit', compact('soal', 'mapels'));
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'mapel_id' => 'required|exists:mapels,id',
            'tipe_soal' => 'required|in:pilihan_ganda,esai,benar_salah',
            'pertanyaan' => 'required|string',
            'opsi_a' => 'nullable|string',
            'opsi_b' => 'nullable|string',
            'opsi_c' => 'nullable|string',
            'opsi_d' => 'nullable|string',
            'jawaban_benar' => 'nullable|string',
            'kunci_jawaban' => 'nullable|string',
        ]);

        $soal = Banksoal::findOrFail($id);

        $data = [
            'mapel_id' => $request->mapel_id,
            'pertanyaan' => $request->pertanyaan,
            'tipe_soal' => $request->tipe_soal,
            'bobot_nilai' => $request->bobot_nilai ?? 10,
            'tingkat_kesulitan' => $request->tingkat_kesulitan ?? 'sedang',
        ];

        if ($request->tipe_soal === 'pilihan_ganda') {
            $data['opsi_a'] = $request->opsi_a;
            $data['opsi_b'] = $request->opsi_b;
            $data['opsi_c'] = $request->opsi_c;
            $data['opsi_d'] = $request->opsi_d;
            $data['opsi_e'] = $request->opsi_e;
            $data['jawaban_benar'] = strtoupper($request->jawaban_benar);
            $data['kunci_jawaban'] = null;
        } elseif ($request->tipe_soal === 'benar_salah') {
            $data['kunci_jawaban'] = $request->kunci_jawaban;
            $data['opsi_a'] = null;
            $data['opsi_b'] = null;
            $data['opsi_c'] = null;
            $data['opsi_d'] = null;
            $data['opsi_e'] = null;
            $data['jawaban_benar'] = strtoupper($request->jawaban_benar);
        } else {
            $data['kunci_jawaban'] = $request->kunci_jawaban;
            $data['opsi_a'] = null;
            $data['opsi_b'] = null;
            $data['opsi_c'] = null;
            $data['opsi_d'] = null;
            $data['opsi_e'] = null;
            $data['jawaban_benar'] = null;
        }

        $soal->update($data);

        return redirect()->route('banksoal.index')->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $soal = Banksoal::findOrFail($id);
        $soal->delete();

        return redirect()->route('banksoal.index')->with('success', 'Soal berhasil dihapus.');
    }

    // Excel Import Methods
    public function importQuestions()
    {
        $mapels = Mapel::all();
        return view('pages.guru.banksoal.import-questions', compact('mapels'));
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

        // Store preview data in session for confirmation
        if ($result['success']) {
            session([
                'import_preview_data' => $result['preview'],
                'import_mapel_id' => $request->mapel_id,
                'import_total_questions' => $result['total_questions']
            ]);
        }

        return view('pages.guru.banksoal.preview-import', compact('result', 'mapel'));
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'mapel_id' => 'required|exists:mapels,id'
        ]);

        $guru = Guru::where('user_id', Auth::id())->first();
        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $importService = new ExcelImportService();

        try {
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
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengimpor soal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function confirmImport(Request $request)
    {
        // Check if preview data exists in session
        if (!session()->has('import_preview_data')) {
            return redirect()->route('banksoal.import_questions')
                ->with('error', 'Data preview tidak ditemukan. Silakan upload file lagi.');
        }

        $previewData = session('import_preview_data');
        $mapel_id = session('import_mapel_id');

        $guru = Guru::where('user_id', Auth::id())->first();
        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // Handle modified, deleted, and new questions if provided
        $modifiedQuestions = json_decode($request->modified_questions ?? '{}', true);
        $deletedQuestions = json_decode($request->deleted_questions ?? '[]', true);
        $newQuestions = json_decode($request->new_questions ?? '[]', true);

        // Remove deleted questions from preview data
        foreach ($deletedQuestions as $index) {
            unset($previewData[$index]);
        }

        // Apply modifications to preview data
        foreach ($modifiedQuestions as $index => $modifiedData) {
            if (isset($previewData[$index])) {
                $previewData[$index] = array_merge($previewData[$index], $modifiedData);
            }
        }

        // Add new questions to preview data
        foreach ($newQuestions as $newQuestion) {
            // Remove temporary fields
            unset($newQuestion['temp_index']);
            unset($newQuestion['is_new']);
            $previewData[] = $newQuestion;
        }

        // Reindex array
        $previewData = array_values($previewData);

        $imported = 0;
        $errors = [];

        try {
            foreach ($previewData as $questionData) {
                // Ensure guru_id is set correctly
                $questionData['guru_id'] = $guru->id;
                $questionData['mapel_id'] = $mapel_id;

                Banksoal::create($questionData);
                $imported++;
            }

            // Clear session data after successful import
            session()->forget(['import_preview_data', 'import_mapel_id', 'import_total_questions']);

            return redirect()->route('banksoal.index')
                ->with('success', "Berhasil mengimpor {$imported} soal dari Excel");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengimpor soal: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $importService = new ExcelImportService();
        $template = $importService->generateTemplate();

        return Excel::download(new QuestionTemplateExport($template), 'template_soal_banksoal.xlsx');
    }

    public function cancelImport()
    {
        // Clear import session data
        session()->forget(['import_preview_data', 'import_mapel_id', 'import_total_questions']);

        return redirect()->route('banksoal.import_questions')
            ->with('info', 'Import dibatalkan.');
    }
}
