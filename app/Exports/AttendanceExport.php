<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $absensis;

    public function __construct($absensis)
    {
        $this->absensis = $absensis;
    }

    public function collection()
    {
        return $this->absensis;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Siswa',
            'Kelas',
            'Mata Pelajaran',
            'Status',
            'Keterangan'
        ];
    }

    public function map($absensi): array
    {
        return [
            $absensi->tanggal->format('d/m/Y'),
            $absensi->siswa->nama,
            $absensi->kelas->nama,
            $absensi->mapel->nama,
            ucfirst($absensi->status),
            $absensi->keterangan ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => ['font' => ['bold' => true]],
        ];
    }
}