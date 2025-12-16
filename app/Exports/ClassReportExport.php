<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClassReportExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function array(): array
    {
        $data = [];
        
        // Add class info
        $data[] = ['Kelas', $this->reportData['kelas']->nama];
        $data[] = ['Mata Pelajaran', $this->reportData['mapel']->nama];
        $data[] = []; // Empty row
        
        // Add statistics
        $data[] = ['STATISTIK KELAS'];
        $data[] = ['Rata-rata Kelas', $this->reportData['statistics']['class_average']];
        $data[] = ['Nilai Tertinggi', $this->reportData['statistics']['highest_score']];
        $data[] = ['Nilai Terendah', $this->reportData['statistics']['lowest_score']];
        $data[] = ['Tingkat Kelulusan (%)', $this->reportData['statistics']['pass_rate']];
        $data[] = ['Total Siswa', $this->reportData['statistics']['total_students']];
        $data[] = ['Total Ujian', $this->reportData['statistics']['total_exams']];
        $data[] = []; // Empty row
        
        // Add student rankings
        $data[] = ['PERINGKAT SISWA'];
        $data[] = ['Peringkat', 'Nama Siswa', 'Rata-rata Nilai', 'Jumlah Ujian', 'Nilai Terakhir'];
        
        $rank = 1;
        foreach ($this->reportData['student_rankings'] as $ranking) {
            $data[] = [
                $rank++,
                $ranking['student']->name,
                round($ranking['average_score'], 2),
                $ranking['exam_count'],
                $ranking['latest_score']
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
            4 => ['font' => ['bold' => true]],
            12 => ['font' => ['bold' => true]],
            13 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Kelas';
    }
}