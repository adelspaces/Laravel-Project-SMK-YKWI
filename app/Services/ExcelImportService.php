<?php

namespace App\Services;

use App\Models\Banksoal;
use App\Models\Mapel;
use App\Models\Guru;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ExcelImportService
{
    public function importQuestions($file, $mapel_id, $preview_only = false)
    {
        try {
            $data = Excel::toArray([], $file)[0]; // Get first sheet
            $errors = [];
            $validQuestions = [];
            $previewData = [];

            // Skip header row
            array_shift($data);


            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because we skipped header and array is 0-indexed

                // Skip empty rows
                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $validation = $this->validateQuestionRow($row, $rowNumber);

                if (!empty($validation['errors'])) {
                    $errors = array_merge($errors, $validation['errors']);
                    continue;
                }

                $questionData = $this->processQuestionRow($row, $mapel_id);

                if ($preview_only) {
                    $previewData[] = array_merge($questionData, ['row_number' => $rowNumber]);
                } else {
                    $validQuestions[] = $questionData;
                }
            }

            if ($preview_only) {
                return [
                    'success' => empty($errors),
                    'errors' => $errors,
                    'preview' => $previewData,
                    'total_questions' => count($previewData)
                ];
            }

            if (!empty($errors)) {
                return [
                    'success' => false,
                    'errors' => $errors
                ];
            }

            // Import questions to database
            $imported = 0;
            foreach ($validQuestions as $questionData) {
                Banksoal::create($questionData);
                $imported++;
            }

            return [
                'success' => true,
                'imported_count' => $imported,
                'errors' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'errors' => ['File error: ' . $e->getMessage()]
            ];
        }
    }

    private function validateQuestionRow($row, $rowNumber)
    {
        $errors = [];

        // Check if question is not empty
        if (empty(trim($row[0] ?? ''))) {
            $errors[] = "Row {$rowNumber}: Pertanyaan tidak boleh kosong";
        }

        // Check if question type is valid
        $validTypes = ['pilihan_ganda', 'esai', 'benar_salah'];
        $tipe = strtolower(trim($row[1] ?? ''));
        if (!in_array($tipe, $validTypes)) {
            $errors[] = "Row {$rowNumber}: Tipe soal harus salah satu dari: " . implode(', ', $validTypes);
        }

        // For multiple choice, check if options are provided
        if ($tipe === 'pilihan_ganda') {
            for ($i = 2; $i <= 6; $i++) { // Columns C-G (opsi A-E)
                // Only validate options A-D as required, E is optional
                if ($i <= 6 && empty(trim($row[$i] ?? ''))) {
                    $optionLabel = chr(65 + ($i - 2)); // A, B, C, D
                    // Only require A-D to be filled
                    if ($i <= 5) {
                        $errors[] = "Row {$rowNumber}: Opsi {$optionLabel} tidak boleh kosong untuk soal pilihan ganda";
                    }
                }
            }
        }

        // Check if answer key is provided
        if (empty(trim($row[7] ?? ''))) {
            $errors[] = "Row {$rowNumber}: Kunci jawaban tidak boleh kosong";
        }

        return ['errors' => $errors];
    }

    private function processQuestionRow($row, $mapel_id)
    {
        $tipe = strtolower(trim($row[1] ?? ''));

        // Get guru_id from the authenticated user
        $guru = Guru::where('user_id', Auth::id())->first();
        $guru_id = $guru ? $guru->id : null;

        // Ensure we have a valid guru_id
        if (!$guru_id) {
            throw new Exception('Guru data not found for authenticated user');
        }

        return [
            'mapel_id' => $mapel_id,
            'guru_id' => $guru_id,
            'pertanyaan' => trim($row[0] ?? ''),
            'tipe_soal' => $tipe,
            'opsi_a' => $tipe === 'pilihan_ganda' ? trim($row[2] ?? '') : null,
            'opsi_b' => $tipe === 'pilihan_ganda' ? trim($row[3] ?? '') : null,
            'opsi_c' => $tipe === 'pilihan_ganda' ? trim($row[4] ?? '') : null,
            'opsi_d' => $tipe === 'pilihan_ganda' ? trim($row[5] ?? '') : null,
            'opsi_e' => ($tipe === 'pilihan_ganda' && !empty(trim($row[6] ?? ''))) ? trim($row[6] ?? '') : null,
            'kunci_jawaban' => trim($row[7] ?? ''),
            'jawaban_benar' => $tipe === 'pilihan_ganda' ? strtoupper(trim($row[7] ?? '')) : trim($row[7] ?? ''),
            'bobot_nilai' => !empty($row[8]) ? (int)$row[8] : 10,
            'tingkat_kesulitan' => !empty($row[9]) ? strtolower(trim($row[9])) : 'sedang'
        ];
    }

    public function generateTemplate()
    {
        $headers = [
            'Pertanyaan',
            'Tipe Soal',
            'Opsi A',
            'Opsi B',
            'Opsi C',
            'Opsi D',
            'Opsi E',
            'Kunci Jawaban',
            'Bobot Nilai',
            'Tingkat Kesulitan'
        ];

        $sampleData = [
            [
                'Apa ibukota Indonesia?',
                'pilihan_ganda',
                'Jakarta',
                'Bandung',
                'Surabaya',
                'Medan',
                'Yogyakarta',
                'A',
                '10',
                'mudah'
            ],
            [
                'Jelaskan pengertian fotosintesis',
                'esai',
                '',
                '',
                '',
                '',
                '',
                'Proses pembuatan makanan oleh tumbuhan menggunakan sinar matahari',
                '20',
                'sedang'
            ]
        ];

        return [
            'headers' => $headers,
            'sample' => $sampleData
        ];
    }

    /**
     * Check if a row is empty (all cells are empty)
     *
     * @param array $row
     * @return bool
     */
    private function isEmptyRow($row)
    {
        foreach ($row as $cell) {
            if (!empty(trim($cell ?? ''))) {
                return false;
            }
        }
        return true;
    }

    public function validateFile($file)
    {
        $errors = [];

        // Check if file is valid
        if (!$file || !is_object($file) || !method_exists($file, 'getClientOriginalExtension')) {
            $errors[] = 'File tidak valid';
            return $errors;
        }

        // Check file extension
        $allowedExtensions = ['xlsx', 'xls', 'csv'];
        $extension = $file->getClientOriginalExtension();

        if (!in_array(strtolower($extension), $allowedExtensions)) {
            $errors[] = 'Format file tidak didukung. Gunakan file Excel (.xlsx, .xls) atau CSV';
        }

        // Check file size (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            $errors[] = 'Ukuran file terlalu besar. Maksimal 5MB';
        }

        return $errors;
    }
}
