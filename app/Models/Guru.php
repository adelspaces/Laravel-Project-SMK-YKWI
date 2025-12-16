<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'mapel_id',
        'jurusan_id',
        'no_telp',
        'alamat',
        'foto',
    ];

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'jadwals', 'guru_id', 'kelas_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
