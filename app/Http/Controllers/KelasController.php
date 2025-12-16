<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kelas = Kelas::with(['guru', 'jurusan'])->get();
        $gurus = Guru::with('jurusan')->get();
        $jurusans = Jurusan::all();
        return view('pages.admin.kelas.index', compact('kelas', 'gurus', 'jurusans'));
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
            'nama_kelas' => 'required|unique:kelas',
            'guru_id' => 'required|unique:kelas',
            'jurusan_id' => 'required|exists:jurusans,id'
        ], [
            'nama_kelas.unique' => 'Nama Kelas sudah ada',
            'guru_id.unique' => 'Guru sudah memiliki kelas',
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'guru_id' => $request->guru_id,
            'jurusan_id' => $request->jurusan_id,
        ]);

        return redirect()->back()->with('success', 'Data kelas berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $id = Crypt::decrypt($id);
        $kelas = Kelas::findOrFail($id);
        $guru = Guru::all();
        $jurusans = Jurusan::all();
        return view('pages.admin.kelas.edit', compact('kelas', 'guru', 'jurusans'));
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
            'nama_kelas' => 'required|string|max:255',
            'guru_id' => 'required|exists:gurus,id',
            'jurusan_id' => 'required|exists:jurusans,id',
        ]);

        // Cek jika guru_id dipakai oleh kelas lain
        $cekGuru = Kelas::where('guru_id', $request->guru_id)
            ->where('id', '!=', $id)
            ->first();
        if ($cekGuru) {
            return redirect()->back()->with('error', 'Guru sudah memiliki kelas lain');
        }

        // Cek jika jurusan_id dipakai oleh kelas lain
        $cekJurusan = Kelas::where('jurusan_id', $request->jurusan_id)
            ->where('id', '!=', $id)
            ->first();
        if ($cekJurusan) {
            return redirect()->back()->with('error', 'Jurusan untuk kelas ini sudah ada');
        }

        $kelas = Kelas::findOrFail($id);
        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'guru_id' => $request->guru_id,
            'jurusan_id' => $request->jurusan_id,
        ]);

        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil dihapus');
    }
}
