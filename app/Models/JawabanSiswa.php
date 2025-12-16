<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanSiswa extends Model
{
    use HasFactory;

    protected $table = 'jawaban_siswas';

    protected $fillable = [
        'user_id',
        'kuis_ujian_id',
        'banksoal_id',
        'jawaban',
        'nilai',
        'status_penilaian',
        'feedback'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kuisUjian()
    {
        return $this->belongsTo(KuisUjian::class, 'kuis_ujian_id');
    }

    public function banksoal()
    {
        return $this->belongsTo(Banksoal::class, 'banksoal_id');
    }
}