<?php

namespace App\Http\Controllers;

use App\Models\MasterAbsensi;
use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsensiSiswaController extends Controller
{
    public function __construct()
    {
        // Ensure only students can access this controller
        $this->middleware('checkRole:siswa');
    }

    /**
     * Display a listing of available master attendance records for the student.
     */
    public function index()
    {
        $siswa = Auth::user()->siswa;

        // Check if student record exists
        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get master attendance records for this student's class
        $masterAbsensis = MasterAbsensi::with(['kelas', 'mapel', 'guru'])
            ->where('kelas_id', $siswa->kelas_id)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        // Get student's attendance records for these master records
        $masterIds = $masterAbsensis->pluck('id');
        $studentAttendances = AbsensiSiswa::with('masterAbsensi')
            ->where('siswa_id', $siswa->id)
            ->whereIn('master_absensi_id', $masterIds)
            ->get()
            ->keyBy('master_absensi_id');

        return view('pages.siswa.absensi.index', compact('masterAbsensis', 'studentAttendances', 'siswa'));
    }

    /**
     * Submit attendance status for a master attendance record.
     */
    public function submitAttendance(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:hadir,izin,sakit'
        ]);

        $siswa = Auth::user()->siswa;

        // Check if student record exists
        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $masterAbsensi = MasterAbsensi::findOrFail($id);

        // Ensure the master attendance record is for this student's class
        if ($masterAbsensi->kelas_id != $siswa->kelas_id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        try {
            DB::beginTransaction();

            // Check if student has already submitted attendance for this master record
            $existingAttendance = AbsensiSiswa::where('siswa_id', $siswa->id)
                ->where('master_absensi_id', $masterAbsensi->id)
                ->first();

            if ($existingAttendance) {
                // If already validated by teacher, prevent changes
                if ($existingAttendance->is_teacher_validated) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Absensi ini sudah divalidasi oleh guru dan tidak dapat diubah.');
                }

                // Update existing record
                $existingAttendance->update([
                    'status' => $request->status
                ]);
            } else {
                // Create new attendance record
                AbsensiSiswa::create([
                    'master_absensi_id' => $masterAbsensi->id,
                    'siswa_id' => $siswa->id,
                    'status' => $request->status
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Absensi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    /**
     * Display attendance statistics for the student.
     */
    public function statistics(Request $request)
    {
        $siswa = Auth::user()->siswa;

        // Check if student record exists
        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Get all master attendance records for this student's class
        $masterAbsensis = MasterAbsensi::where('kelas_id', $siswa->kelas_id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();

        // Get student's attendance records for these master records
        $masterIds = $masterAbsensis->pluck('id');
        $studentAttendances = AbsensiSiswa::with('masterAbsensi')
            ->where('siswa_id', $siswa->id)
            ->whereIn('master_absensi_id', $masterIds)
            ->get();

        // Calculate statistics
        $totalDays = $masterAbsensis->count();
        $presentDays = $studentAttendances->where('status', 'hadir')->count();
        $izinDays = $studentAttendances->where('status', 'izin')->count();
        $sakitDays = $studentAttendances->where('status', 'sakit')->count();

        $attendanceRate = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;

        return view('pages.siswa.absensi.statistics', compact(
            'siswa',
            'studentAttendances',
            'masterAbsensis',
            'totalDays',
            'presentDays',
            'izinDays',
            'sakitDays',
            'attendanceRate',
            'month',
            'year'
        ));
    }
}
