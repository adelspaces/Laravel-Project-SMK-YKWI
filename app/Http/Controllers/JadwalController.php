<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Guru; // Added Guru model
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jadwal = Jadwal::orderBy('hari', 'desc')->get();
        $mapel = Mapel::orderBy('nama_mapel', 'desc')->get();
        $kelas = Kelas::orderBy('nama_kelas', 'desc')->get();
        $guru = Guru::orderBy('nama', 'desc')->get(); // Added guru data
        return view('pages.admin.jadwal.index', compact('jadwal', 'mapel', 'kelas', 'guru'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $this->validate($request, [
            'kelas_id' => 'required',
            'mapel_id' => 'required|unique:jadwals,dari_jam',
            'hari' => 'required',
            'dari_jam' => 'required',
            'sampai_jam' => 'required',
            'guru_id' => 'required' // Added guru_id validation
        ], [
            'kelas_id.required' => 'Kelas wajib diisi',
            'mapel_id.required' => 'Mata Pelajaran wajib diisi',
            'mapel_id.unique' => 'Mata Pelajaran sudah ada di jam tersebut',
            'hari.required' => 'Hari wajib diisi',
            'dari_jam.required' => 'Jam mulai wajib diisi',
            'sampai_jam.required' => 'Jam selesai wajib diisi',
            'guru_id.required' => 'Guru wajib diisi' // Added guru_id validation message
        ]);

        Jadwal::create([
            'kelas_id' => $data['kelas_id'],
            'mapel_id' => $data['mapel_id'],
            'hari' => $data['hari'],
            'dari_jam' => $data['dari_jam'],
            'sampai_jam' => $data['sampai_jam'],
            'guru_id' => $data['guru_id'] // Added guru_id to creation
        ]);

        return redirect()->back()->with('success', 'Jadwal berhasil dibuat');
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
        $jadwal = Jadwal::find($id);
        $mapel = Mapel::orderBy('nama_mapel', 'desc')->get();
        $kelas = Kelas::orderBy('nama_kelas', 'desc')->get();
        $guru = Guru::orderBy('nama', 'desc')->get(); // Added guru data

        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];

        return view('pages.admin.jadwal.edit', compact('jadwal', 'mapel', 'kelas', 'hari', 'guru'));
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
        $data = $request->all();

        $this->validate($request, [
            'kelas_id' => 'required',
            'mapel_id' => 'required',
            'hari' => 'required',
            'dari_jam' => 'required',
            'sampai_jam' => 'required',
            'guru_id' => 'required' // Added guru_id validation
        ], [
            'kelas_id.required' => 'Kelas wajib diisi',
            'mapel_id.required' => 'Mata Pelajaran wajib diisi',
            'hari.required' => 'Hari wajib diisi',
            'dari_jam.required' => 'Jam mulai wajib diisi',
            'sampai_jam.required' => 'Jam selesai wajib diisi',
            'guru_id.required' => 'Guru wajib diisi' // Added guru_id validation message
        ]);

        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update($data);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbaharui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jadwal = Jadwal::find($id);
        $jadwal->delete();

        return redirect()->back()->with('success', 'Jadwal berhasil dihapus');
    }
}
