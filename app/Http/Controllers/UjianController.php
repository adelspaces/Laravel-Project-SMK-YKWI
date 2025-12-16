<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\HasilUjian;
use Illuminate\Support\Facades\Auth;
use App\Models\Banksoal;

class UjianController extends Controller
{
    public function kerjakan($id)
    {
        $ujian = Ujian::findOrFail($id);

        // Proteksi waktu ujian
        if (now() < $ujian->waktu_mulai || now() > $ujian->waktu_selesai) {
            return redirect()->back()->with('error', 'Ujian belum dimulai atau sudah berakhir.');
        }

        $soals = $ujian->soals; // pastikan relasi `soals()` ada di model Ujian
        return view('pages.siswa.ujian.kerjakan', compact('ujian', 'soals'));
    }

    public function submit(Request $request, $id)
    {
        $ujian = Ujian::findOrFail($id);
        $jawabanInput = $request->input('jawaban');

        foreach ($jawabanInput as $soal_id => $jawaban) {
            HasilUjian::create([
                'ujian_id' => $ujian->id,
                'siswa_id' => Auth::user()->siswa->id,
                'soal_id' => $soal_id,
                'jawaban' => $jawaban,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Jawaban berhasil dikirim!');
    }

    public function index()
    {
        $ujian = Ujian::with('mapel')->get(); // muat relasi mapel
        return view('pages.guru.ujian.index', compact('ujian')); // sesuaikan nama variabel
    }
}
