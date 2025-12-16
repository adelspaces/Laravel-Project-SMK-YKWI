<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;


class Banksoal extends Model
{
    protected $fillable = [
        'mapel_id',
        'guru_id',
        'pertanyaan',
        'tipe_soal',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'jawaban_benar',
        'kunci_jawaban',
        'bobot_nilai',
        'tingkat_kesulitan'
    ];

    // RELASI: Banksoal milik satu Mapel
    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kuisUjians()
    {
        return $this->belongsToMany(KuisUjian::class, 'banksoal_kuis_ujian', 'banksoal_id', 'kuis_ujian_id');
    }
    public function jawabanSiswas()
    {
        return $this->hasMany(JawabanSiswa::class, 'banksoal_id');
    }
}
