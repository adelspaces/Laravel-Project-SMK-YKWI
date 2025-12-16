<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id',
        'siswa_id',
        'kelas_id',
        'mapel_id',
        'tanggal',
        'status',
        'keterangan',
        'jam_masuk',
        'jam_keluar',
        'pertemuan',           // New field
        'is_student_submitted', // New field
        'is_teacher_edited'     // New field
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime',
        'jam_keluar' => 'datetime',
        'is_student_submitted' => 'boolean',
        'is_teacher_edited' => 'boolean'
    ];

    // Relasi ke tabel Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    // Relasi ke tabel Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke tabel Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    // Relasi ke tabel Mapel
    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    // Accessors for better readability
    public function isStudentSubmitted()
    {
        return $this->is_student_submitted;
    }

    public function isTeacherEdited()
    {
        return $this->is_teacher_edited;
    }

    // Business Logic Methods
    public static function getAttendancePercentage($siswa_id, $start_date, $end_date)
    {
        $totalDays = self::where('siswa_id', $siswa_id)
            ->whereBetween('tanggal', [$start_date, $end_date])
            ->count();
            
        $presentDays = self::where('siswa_id', $siswa_id)
            ->whereBetween('tanggal', [$start_date, $end_date])
            ->where('status', 'hadir')
            ->count();
            
        return $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;
    }

    public static function getMonthlyReport($kelas_id, $month, $year)
    {
        return self::with(['siswa', 'mapel'])
            ->where('kelas_id', $kelas_id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get()
            ->groupBy('siswa_id');
    }

    public static function bulkUpdate(array $data)
    {
        $results = [];
        foreach ($data as $item) {
            $absensi = self::updateOrCreate(
                [
                    'guru_id' => $item['guru_id'],
                    'siswa_id' => $item['siswa_id'] ?? null,
                    'kelas_id' => $item['kelas_id'],
                    'mapel_id' => $item['mapel_id'],
                    'tanggal' => $item['tanggal']
                ],
                [
                    'status' => $item['status'],
                    'keterangan' => $item['keterangan'] ?? null,
                    'jam_masuk' => $item['jam_masuk'] ?? null,
                    'jam_keluar' => $item['jam_keluar'] ?? null,
                    'pertemuan' => $item['pertemuan'] ?? null,           // New field
                    'is_student_submitted' => $item['is_student_submitted'] ?? false, // New field
                    'is_teacher_edited' => $item['is_teacher_edited'] ?? false     // New field
                ]
            );
            $results[] = $absensi;
        }
        return $results;
    }

    public function getStatusColor()
    {
        return match($this->status) {
            'hadir' => 'success',
            'izin' => 'warning',
            'sakit' => 'info',
            'alfa' => 'danger',
            default => 'secondary'
        };
    }
}