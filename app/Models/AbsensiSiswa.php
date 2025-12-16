<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiSiswa extends Model
{
    use HasFactory;

    protected $table = 'absensi_siswa';

    protected $fillable = [
        'master_absensi_id',
        'siswa_id',
        'status',
        'is_teacher_validated'
    ];

    protected $casts = [
        'is_teacher_validated' => 'boolean'
    ];

    // Relasi ke tabel MasterAbsensi
    public function masterAbsensi()
    {
        return $this->belongsTo(MasterAbsensi::class, 'master_absensi_id');
    }

    // Relasi ke tabel Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}