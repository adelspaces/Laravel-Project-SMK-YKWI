<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Materi;
use App\Models\PengumumanSekolah;
use App\Models\Siswa;
use App\Models\Tugas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function admin()
    {
        $siswa = Siswa::count();
        $guru = Guru::count();
        $kelas = Kelas::count();
        $mapel = Mapel::count();
        $siswaBaru = Siswa::orderByDesc('id')->take(5)->orderBy('id')->first();

        return view('pages.admin.dashboard', compact('siswa', 'guru', 'kelas', 'mapel', 'siswaBaru'));
    }

    public function guru()
    {
        $guru = Guru::where('user_id', Auth::user()->id)->first();

        // Handle case where guru record doesn't exist
        if (!$guru) {
            return view('pages.guru.dashboard', [
                'guru' => null,
                'materi' => 0,
                'jadwal' => collect(),
                'tugas' => 0,
                'hari' => Carbon::now()->locale('id')->isoFormat('dddd')
            ]);
        }

        $materi = Materi::where('guru_id', $guru->id ?? 0)->count();
        $jadwal = $guru->mapel_id ? Jadwal::where('mapel_id', $guru->mapel_id)->get() : collect();
        $tugas = Tugas::where('guru_id', $guru->id ?? 0)->count();
        $hari = Carbon::now()->locale('id')->isoFormat('dddd');

        return view('pages.guru.dashboard', compact('guru', 'materi', 'jadwal', 'hari', 'tugas'));
    }

    public function siswa()
    {
        $siswa = Siswa::where('nis', Auth::user()->nis)->first();
        // Handle case where siswa record doesn't exist
        if (!$siswa) {
            return view('pages.siswa.dashboard', [
                'materi' => collect(),
                'siswa' => null,
                'kelas' => null,
                'tugas' => collect(),
                'jadwal' => collect(),
                'hari' => Carbon::now()->locale('id')->isoFormat('dddd'),
                'pengumumans' => PengumumanSekolah::active()->get()
            ]);
        }

        $kelas = $siswa->kelas_id ? Kelas::find($siswa->kelas_id) : null;
        if (!$kelas) {
            return view('pages.siswa.dashboard', [
                'materi' => collect(),
                'siswa' => $siswa,
                'kelas' => null,
                'tugas' => collect(),
                'jadwal' => collect(),
                'hari' => Carbon::now()->locale('id')->isoFormat('dddd'),
                'pengumumans' => PengumumanSekolah::active()->get()
            ]);
        }

        $materi = Materi::where('kelas_id', $kelas->id)->limit(3)->get();
        $tugas = Tugas::where('kelas_id', $kelas->id)->limit(3)->get();
        $jadwal = Jadwal::where('kelas_id', $kelas->id)->get();
        $hari = Carbon::now()->locale('id')->isoFormat('dddd');
        $pengumumans = PengumumanSekolah::active()->get();
        return view('pages.siswa.dashboard', compact('materi', 'siswa', 'kelas', 'tugas', 'jadwal', 'hari', 'pengumumans'));
    }
}
