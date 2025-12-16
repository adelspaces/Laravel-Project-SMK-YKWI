<?php

namespace App\Http\Controllers;

use App\Models\MasterAbsensi;
use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterAbsensiController extends Controller
{
    /**
     * Display a listing of master attendance records.
     */
    public function index(Request $request)
    {
        $guru = Auth::user()->guru;

        $query = MasterAbsensi::with(['kelas.jurusan', 'mapel'])
            ->where('guru_id', $guru->id);

        // Apply filters
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('mapel_id')) {
            $query->where('mapel_id', $request->mapel_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $masterAbsensis = $query->orderBy('tanggal', 'desc')
            ->paginate(20);

        // Get unique classes assigned to this teacher
        $kelasIds = $guru->jadwal()->pluck('kelas_id')->unique();
        $kelas = Kelas::with('jurusan')->whereIn('id', $kelasIds)->get();

        // Get unique subjects assigned to this teacher
        $mapelIds = $guru->jadwal()->pluck('mapel_id')->unique();
        $mapel = Mapel::whereIn('id', $mapelIds)->get();

        return view('pages.guru.master-absensi.index', compact('masterAbsensis', 'kelas', 'mapel'));
    }

    /**
     * Show the form for creating a new master attendance record.
     */
    public function create()
    {
        $guru = Auth::user()->guru;

        // Get unique classes assigned to this teacher through Jadwal
        $kelasIds = $guru->jadwal()->pluck('kelas_id')->unique();

        $kelas = Kelas::with('jurusan')->whereIn('id', $kelasIds)->get();

        // Get unique subjects assigned to this teacher
        $mapelIds = $guru->jadwal()->pluck('mapel_id')->unique();
        $mapel = Mapel::whereIn('id', $mapelIds)->get();

        return view('pages.guru.master-absensi.create', compact('kelas', 'mapel'));
    }

    /**
     * Store a newly created master attendance record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'pertemuan' => 'required|integer|min:1|max:100'
        ]);

        $guru = Auth::user()->guru;

        // Check if teacher is assigned to this class and subject through Jadwal
        $isAssignedToClass = $guru->jadwal()->where('kelas_id', $request->kelas_id)->exists();
        $isAssignedToSubject = $guru->jadwal()->where('mapel_id', $request->mapel_id)->exists();

        if (!$isAssignedToClass || !$isAssignedToSubject) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki akses untuk membuat absensi untuk kelas atau mata pelajaran ini.')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            MasterAbsensi::create([
                'guru_id' => $guru->id,
                'kelas_id' => $request->kelas_id,
                'mapel_id' => $request->mapel_id,
                'tanggal' => $request->tanggal,
                'pertemuan' => $request->pertemuan
            ]);

            DB::commit();

            return redirect()->route('absensi.index')
                ->with('success', 'Master absensi berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membuat master absensi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display student attendance records for a master attendance record.
     */
    public function showStudentAttendance($id)
    {
        $masterAbsensi = MasterAbsensi::with(['kelas.jurusan', 'mapel', 'absensiSiswa.siswa'])->findOrFail($id);

        // Ensure the teacher owns this master attendance record
        if ($masterAbsensi->guru_id != Auth::user()->guru->id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        return view('pages.guru.master-absensi.student-attendance', compact('masterAbsensi'));
    }

    /**
     * Show the form for editing student attendance.
     */
    public function editStudentAttendance($id)
    {
        $absensiSiswa = AbsensiSiswa::with(['masterAbsensi', 'siswa'])->findOrFail($id);

        // Ensure the teacher owns this master attendance record
        if ($absensiSiswa->masterAbsensi->guru_id != Auth::user()->guru->id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        return view('pages.guru.master-absensi.edit-student-attendance', compact('absensiSiswa'));
    }

    /**
     * Update student attendance status.
     */
    public function updateStudentAttendance(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:hadir,izin,sakit'
        ]);

        $absensiSiswa = AbsensiSiswa::findOrFail($id);

        // Ensure the teacher owns this master attendance record
        if ($absensiSiswa->masterAbsensi->guru_id != Auth::user()->guru->id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        try {
            $absensiSiswa->update([
                'status' => $request->status,
                'is_teacher_validated' => true
            ]);

            return redirect()->route('absensi.master.show-student-attendance', $absensiSiswa->master_absensi_id)
                ->with('success', 'Status absensi siswa berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui status absensi siswa: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified master attendance record from storage.
     */
    public function destroy($id)
    {
        $masterAbsensi = MasterAbsensi::findOrFail($id);

        // Ensure the teacher owns this master attendance record
        if ($masterAbsensi->guru_id != Auth::user()->guru->id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        try {
            DB::beginTransaction();

            // Delete related student attendance records first
            AbsensiSiswa::where('master_absensi_id', $masterAbsensi->id)->delete();

            // Delete the master attendance record
            $masterAbsensi->delete();

            DB::commit();

            return redirect()->route('absensi.index')->with('success', 'Data master absensi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data master absensi: ' . $e->getMessage());
        }
    }
}
