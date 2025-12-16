<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\Materi;
use App\Models\Kelas;
use App\Models\Tugas;
use Carbon\Carbon;


class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mapel = Mapel::orderBy('nama_mapel', 'asc')->get();
        // Load guru with mapel relationship instead of jadwal
        $guru = Guru::with('mapel.jurusan')->orderBy('nama', 'asc')->get();
        $materi = Materi::count();
        $tugas = Tugas::count();

        return view('pages.admin.guru.index', compact('guru', 'mapel', 'materi', 'tugas'));
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'nama' => 'required',
            'nip' => 'required|unique:gurus',
            'no_telp' => 'required',
            'alamat' => 'required',
            'mapel_id' => 'required',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ], [
            'nip.unique' => 'NIP sudah terdaftar',
        ]);

        if (isset($request->foto)) {
            $file = $request->file('foto');
            $namaFoto = time() . '.' . $file->getClientOriginalExtension();
            $foto = $file->storeAs('images/guru', $namaFoto, 'public');
        }

        $guru = new Guru;
        $guru->nama = $request->nama;
        $guru->nip = $request->nip;
        $guru->no_telp = $request->no_telp;
        $guru->alamat = $request->alamat;
        $guru->mapel_id = $request->mapel_id;
        $guru->foto = $foto;
        $guru->save();


        return redirect()->route('guru.index')->with('success', 'Data guru berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $guru = Guru::findOrFail($id);

        return view('pages.admin.guru.profile', compact('guru'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $mapel = Mapel::all();
        $guru = Guru::findOrFail($id);

        return view('pages.admin.guru.edit', compact('guru', 'mapel'));
    }

    public function siswaSaya()
    {
        $guru = Guru::where('user_id', Auth::id())->first();
        $jadwal = Jadwal::where('guru_id', $guru->id)->get();
        $kelasIds = $jadwal->pluck('kelas_id')->unique();
        $siswas = Siswa::whereIn('kelas_id', $kelasIds)->get();

        return view('pages.guru.siswa', compact('siswas'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nip' => 'required|unique:gurus,nip,' . $id,
        ], [
            'nip.unique' => 'NIP sudah terdaftar',
        ]);


        $guru = Guru::find($id);
        $guru->nama = $request->input('nama');
        $guru->nip = $request->input('nip');
        $guru->no_telp = $request->input('no_telp');
        $guru->alamat = $request->input('alamat');
        $guru->mapel_id = $request->input('mapel_id');

        if ($request->hasFile('foto')) {
            $lokasi = 'images/guru/' . $guru->foto;
            if (File::exists($lokasi)) {
                File::delete($lokasi);
            }
            $foto = $request->file('foto');
            $namaFoto = time() . '.' . $foto->getClientOriginalExtension();
            $tujuanFoto = public_path('img/guru');
            $foto->move(public_path('img/guru'), $namaFoto);
            $guru->foto = $namaFoto;
        }

        $guru->update();

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $guru = Guru::find($id);
        $lokasi = 'img/guru/' . $guru->foto;
        $guru->delete();

        // Hapus data user
        if ($user = User::where('id', $guru->user_id)->first()) {
            $user->delete();
        }

        return back()->with('success', 'Data mapel berhasil dihapus!');
    }
    public function dashboard()
    {
        $guru = Guru::where('user_id', Auth::id())->first();

        // Ambil hari ini (misalnya: "Senin", "Selasa", dst.)
        $hariIni = Carbon::now()->locale('id')->translatedFormat('l');

        // Ambil jadwal berdasarkan guru dan hari ini
        $jadwal = Jadwal::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->where('hari', $hariIni)
            ->get();

        // Hitung jumlah materi dan tugas yg dibuat guru ini
        $materi = Materi::where('guru_id', $guru->id)->count();
        $tugas = Tugas::where('guru_id', $guru->id)->count();

        return view('pages.guru.dashboard', compact('guru', 'jadwal', 'materi', 'tugas', 'hariIni'));
    }
}
