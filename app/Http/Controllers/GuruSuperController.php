<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Materi;
use App\Models\Tugas;

class GuruSuperController extends Controller
{
    public function dashboard()
    {
        $jumlahGuru = Guru::count();
        $jumlahSiswa = Siswa::count();
        $jumlahMateri = Materi::count();
        $jumlahTugas = Tugas::count();

        return view('pages.guru_super.dashboard', compact(
            'jumlahGuru',
            'jumlahSiswa',
            'jumlahMateri',
            'jumlahTugas'
        ));
    }
}
