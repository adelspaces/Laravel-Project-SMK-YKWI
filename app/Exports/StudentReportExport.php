<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentReportExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $student;
    protected $reportData;

    public function __construct($student, $reportData)
    {
        $this->student = $student;
        $this->reportData = $reportData;
    }

    public function array(): array
    {
        $data = [];
        
        // Add student info
        $data[] = ['Nama Siswa', $this->student->name];
        $data[] = ['NIS', $this->student->siswa->nis ?? '-'];
        $data[] = ['Kelas', $this->student->siswa->kelas->nama ?? '-'];
        $data[] = []; // Empty row
        
        // Add statistics
        $data[] = ['STATISTIK UJIAN'];
        $data[] = ['Total Ujian', $this->reportData['statistics']['total_exams']];
        $data[] = ['Rata-rata Nilai', $this->reportData['statistics']['average_score']];
        $data[] = ['Nilai Tertinggi', $this->reportData['statistics']['highest_score']];
        $data[] = ['Nilai Terendah', $this->reportData['statistics']['lowest_score']];
        $data[] = ['Tingkat Kehadiran (%)', $this->reportData['statistics']['attendance_rate']];
        $data[] = []; // Empty row
        
        // Add exam results
        $data[] = ['RIWAYAT UJIAN'];
        $data[] = ['Tanggal', 'Judul Ujian', 'Mata Pelajaran', 'Nilai', 'Grade'];
        
        foreach ($this->reportData['exam_results'] as $result) {
            $data[] = [
                $result->created_at->format('d/m/Y'),
                $result->kuisUjian->judul,
                $result->kuisUjian->mapel->nama,
                $result->nilai_total,
                $result->calculateGrade()
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            5 => ['font' => ['bold' => true]],
            13 => ['font' => ['bold' => true]],
            14 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Siswa';
    }
}