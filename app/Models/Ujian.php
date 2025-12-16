<?php

// App\Models\Ujian.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $fillable = [
        'mapel_id',
        'kelas_id',
        'guru_id',
        'judul',
        'waktu_mulai',
        'waktu_selesai',
        'durasi'
    ];

    public function soal()
    {
        return $this->hasMany(Soal::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }
}
