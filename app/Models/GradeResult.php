<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_score',
        'assessment_score',
        'final_score',
        'letter_grade'
    ];

    protected $casts = [
        'attendance_score' => 'decimal:2',
        'assessment_score' => 'decimal:2',
        'final_score' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}