<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $fillable = [
        'pertanyaan',
        'tipe_soal',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'jawaban_benar',
        'guru_id',
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }
}
