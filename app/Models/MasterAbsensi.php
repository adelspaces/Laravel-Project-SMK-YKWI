<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAbsensi extends Model
{
    use HasFactory;

    protected $table = 'master_absensi';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'mapel_id',
        'tanggal',
        'pertemuan'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    // Relasi ke tabel Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class);
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

    // Relasi ke tabel AbsensiSiswa
    public function absensiSiswa()
    {
        return $this->hasMany(AbsensiSiswa::class, 'master_absensi_id');
    }
}