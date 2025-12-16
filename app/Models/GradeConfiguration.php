<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_weight',
        'assessment_weight',
        'tugas_weight',
        'grade_thresholds'
    ];

    protected $casts = [
        'attendance_weight' => 'decimal:2',
        'assessment_weight' => 'decimal:2',
        'tugas_weight' => 'decimal:2',
        'grade_thresholds' => 'array'
    ];
}
