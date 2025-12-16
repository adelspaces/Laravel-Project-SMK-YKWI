<?php

namespace App\Exports;

use App\Models\Tugas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TugasRekapExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tugas;

    public function __construct(Tugas $tugas)
    {
        $this->tugas = $tugas;
    }

    public function collection()
    {
        return $this->tugas->jawaban()->with(['siswa.kelas'])->get();
    }

    public function headings(): array
    {
        return [
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Jawaban',
            'File',
            'Nilai',
            'Tanggal Pengumpulan'
        ];
    }

    public function map($jawaban): array
    {
        return [
            $jawaban->siswa->nis ?? '-',
            $jawaban->siswa->nama ?? '-',
            $jawaban->siswa->kelas->nama_kelas ?? '-',
            $jawaban->jawaban ?? '-',
            $jawaban->file ? url('storage/' . $jawaban->file) : '-',
            $jawaban->nilai ?? '-',
            optional($jawaban->created_at)->format('d-m-Y H:i')
        ];
    }
}
