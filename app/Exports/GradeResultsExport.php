<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GradeResultsExport implements FromCollection, WithHeadings, WithMapping
{
    /** @var \Illuminate\Support\Collection */
    protected $results;

    public function __construct(Collection $results)
    {
        $this->results = $results;
    }

    public function collection()
    {
        return $this->results;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'NIS',
            'Nilai Absensi',
            'Nilai Penilaian',
            'Nilai Akhir',
            'Grade Huruf',
            'Dihitung Pada'
        ];
    }

    public function map($gradeResult): array
    {
        return [
            optional($gradeResult->user)->name,
            optional($gradeResult->user)->email,
            optional($gradeResult->user)->nis,
            number_format((float) ($gradeResult->attendance_score ?? 0), 2, '.', ''),
            number_format((float) ($gradeResult->assessment_score ?? 0), 2, '.', ''),
            number_format((float) ($gradeResult->final_score ?? 0), 2, '.', ''),
            $gradeResult->letter_grade ?? '-',
            optional($gradeResult->calculated_at ?? $gradeResult->updated_at)->format('d-m-Y H:i')
        ];
    }
}
