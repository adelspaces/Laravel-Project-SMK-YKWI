<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilUjian extends Model
{
    use HasFactory;

    protected $table = 'hasil_ujians';

    protected $fillable = [
        'user_id',
        'kuis_ujian_id',
        'total_soal',
        'soal_benar',
        'soal_salah',
        'soal_kosong',
        'nilai_total',
        'grade',
        'status',
        'waktu_mulai',
        'waktu_selesai',
        'durasi_pengerjaan',
        'attempt_number'
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'nilai_total' => 'decimal:2'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kuisUjian()
    {
        return $this->belongsTo(KuisUjian::class);
    }

    // Business Logic Methods
    public function calculateGrade()
    {
        $percentage = $this->nilai_total;
        
        if ($percentage >= 90) {
            return 'A';
        } elseif ($percentage >= 80) {
            return 'B';
        } elseif ($percentage >= 70) {
            return 'C';
        } elseif ($percentage >= 60) {
            return 'D';
        } else {
            return 'F';
        }
    }

    public function getPerformanceCategory()
    {
        $percentage = $this->nilai_total;
        
        if ($percentage >= 85) {
            return 'Sangat Baik';
        } elseif ($percentage >= 70) {
            return 'Baik';
        } elseif ($percentage >= 60) {
            return 'Cukup';
        } else {
            return 'Perlu Perbaikan';
        }
    }

    public function getDetailedAnalysis()
    {
        return [
            'accuracy' => $this->total_soal > 0 ? ($this->soal_benar / $this->total_soal) * 100 : 0,
            'completion_rate' => $this->total_soal > 0 ? (($this->soal_benar + $this->soal_salah) / $this->total_soal) * 100 : 0,
            'time_per_question' => $this->total_soal > 0 && $this->durasi_pengerjaan ? $this->durasi_pengerjaan / $this->total_soal : 0
        ];
    }

    public function compareWithClassAverage()
    {
        $classAverage = self::whereHas('kuisUjian', function($query) {
            $query->where('id', $this->kuis_ujian_id);
        })->avg('nilai_total');

        return [
            'student_score' => $this->nilai_total,
            'class_average' => round($classAverage, 2),
            'difference' => round($this->nilai_total - $classAverage, 2),
            'above_average' => $this->nilai_total > $classAverage
        ];
    }
}
