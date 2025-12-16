<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KuisUjian extends Model
{
    protected $table = 'kuis_ujians';

    protected $fillable = [
        'banksoal_id',
        'mapel_id',
        'guru_id',
        'judul',
        'tipe',
        'waktu_mulai',
        'waktu_selesai',
        'durasi',
        'max_attempt',
        'is_random',
    ];

    protected $casts = [
        'is_random' => 'boolean',
    ];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function banksoals()
    {
        return $this->belongsToMany(Banksoal::class, 'banksoal_kuis_ujian', 'kuis_ujian_id', 'banksoal_id');
    }

    public function jawabanSiswas()
    {
        return $this->hasMany(JawabanSiswa::class, 'kuis_ujian_id');
    }
}
