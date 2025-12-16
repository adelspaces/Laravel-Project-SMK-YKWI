<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Jadwal;
use App\Models\MasterAbsensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use App\Models\AbsensiSiswa;
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    public function __construct()
    {
        // Add a check in the constructor to ensure only teachers can access this controller
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role === 'siswa') {
                return redirect()->route('absensi.siswa.index')
                    ->with('info', 'Anda telah dialihkan ke halaman absensi siswa yang sesuai.');
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $guru = Auth::user()->guru;
        $kelas = Kelas::all();
        $mapel = Mapel::all();

        // Get traditional attendance records
        $traditionalQuery = Absensi::with(['siswa', 'kelas', 'mapel'])
            ->where('guru_id', $guru->id);

        // Get master attendance records
        $masterQuery = \App\Models\MasterAbsensi::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id);

        // Apply filters to both queries
        if ($request->filled('kelas_id')) {
            $traditionalQuery->where('kelas_id', $request->kelas_id);
            $masterQuery->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('mapel_id')) {
            $traditionalQuery->where('mapel_id', $request->mapel_id);
            $masterQuery->where('mapel_id', $request->mapel_id);
        }

        if ($request->filled('tanggal')) {
            $traditionalQuery->whereDate('tanggal', $request->tanggal);
            $masterQuery->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('status')) {
            $traditionalQuery->where('status', $request->status);
        }

        // Filter by student submitted flag (only applies to traditional records)
        if ($request->filled('is_student_submitted')) {
            $traditionalQuery->where('is_student_submitted', $request->is_student_submitted);
        }

        // Get results
        $traditionalAbsensis = $traditionalQuery->get();
        $masterAbsensis = $masterQuery->get();

        // Combine and sort all records by date (newest first)
        $allRecords = collect();

        // Add traditional records with type indicator
        foreach ($traditionalAbsensis as $record) {
            $record->record_type = 'traditional';
            $allRecords->push($record);
        }

        // Add master records with type indicator
        foreach ($masterAbsensis as $record) {
            $record->record_type = 'master';
            $allRecords->push($record);
        }

        // Sort by date (newest first)
        $allRecords = $allRecords->sortByDesc('tanggal')->values();

        // Paginate the combined results
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $currentPageItems = $allRecords->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedRecords = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $allRecords->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        // Get students for selected class if filters are applied (for traditional attendance creation)
        $siswas = [];
        $existingAttendance = [];

        if ($request->filled(['kelas_id', 'mapel_id', 'tanggal'])) {
            $siswas = Siswa::where('kelas_id', $request->kelas_id)
                ->orderBy('nama')
                ->get();

            // Get existing attendance for the date
            $existingAttendance = Absensi::where('kelas_id', $request->kelas_id)
                ->where('mapel_id', $request->mapel_id)
                ->where('tanggal', $request->tanggal)
                ->where('guru_id', $guru->id)
                ->pluck('status', 'siswa_id')
                ->toArray();
        }

        return view('pages.guru.absensi.index', compact(
            'kelas',
            'mapel',
            'siswas',
            'paginatedRecords',
            'existingAttendance'
        ));
    }

    public function create()
    {
        // Redirect to master absensi system
        return redirect()->route('absensi.master.create');
    }

    public function store(Request $request)
    {
        try {
            // Basic validation
            $request->validate([
                'tanggal' => 'required|date',
                'kelas_id' => 'required|exists:kelas,id',
                'mapel_id' => 'required|exists:mapels,id',
                'absen' => 'required|array',
                'absen.*' => 'required|in:hadir,izin,sakit,alfa',
                'pertemuan' => 'nullable|integer|min:1|max:100' // New validation for pertemuan
            ]);

            // Check if user is authenticated and has guru relation
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('error', 'Silakan login terlebih dahulu.');
            }

            $user = Auth::user();
            if (!$user->guru) {
                return redirect()->back()
                    ->with('error', 'Akses ditolak. Hanya guru yang dapat mengakses fitur ini.')
                    ->withInput();
            }

            $guru = $user->guru;

            DB::beginTransaction();

            // Check if any existing attendance records were submitted by students
            $existingStudentRecords = Absensi::where('guru_id', $guru->id)
                ->where('kelas_id', $request->kelas_id)
                ->where('mapel_id', $request->mapel_id)
                ->where('tanggal', $request->tanggal)
                ->where('is_student_submitted', true)
                ->pluck('siswa_id')
                ->toArray();

            // Prepare attendance data
            $attendanceData = [];
            foreach ($request->absen as $siswa_id => $status) {
                // Check if this student already submitted attendance
                $isStudentSubmitted = in_array($siswa_id, $existingStudentRecords);

                $attendanceData[] = [
                    'guru_id' => $guru->id,
                    'siswa_id' => $siswa_id,
                    'kelas_id' => $request->kelas_id,
                    'mapel_id' => $request->mapel_id,
                    'tanggal' => $request->tanggal,
                    'status' => $status,
                    'pertemuan' => $request->pertemuan, // New field
                    'is_teacher_edited' => false, // Teacher is creating this record, not editing
                    'is_student_submitted' => $isStudentSubmitted, // Preserve student submitted flag
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Delete existing attendance for this date/class/subject (but preserve student submissions)
            Absensi::where('guru_id', $guru->id)
                ->where('kelas_id', $request->kelas_id)
                ->where('mapel_id', $request->mapel_id)
                ->where('tanggal', $request->tanggal)
                ->where('is_student_submitted', false) // Only delete teacher-created records
                ->delete();

            // Insert attendance records
            if (!empty($attendanceData)) {
                Absensi::insert($attendanceData);
            }

            DB::commit();

            return redirect()->route('absensi.index')
                ->with('success', 'Absensi berhasil disimpan untuk ' . count($attendanceData) . ' siswa.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving attendance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function riwayat()
    {
        $guru = Auth::user()->guru;

        $absensis = Absensi::with(['siswa', 'kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pages.guru.absensi.riwayat', compact('absensis'));
    }

    public function show($id)
    {
        $absensi = Absensi::with(['siswa', 'kelas', 'mapel', 'guru'])->findOrFail($id);
        return view('pages.guru.absensi.show', compact('absensi'));
    }

    public function edit($id)
    {
        $absensi = Absensi::findOrFail($id);
        $guru = Auth::user()->guru;

        $jadwals = Jadwal::where('guru_id', $guru->id)->get();
        $kelasIds = $jadwals->pluck('kelas_id')->unique();
        $mapelIds = $jadwals->pluck('mapel_id')->unique();

        $kelas = Kelas::whereIn('id', $kelasIds)->get();
        $mapels = Mapel::whereIn('id', $mapelIds)->get();

        // Include the student from the attendance record, even if they're not in the teacher's current classes
        $siswas = Siswa::whereIn('kelas_id', $kelasIds)
            ->orWhere('id', $absensi->siswa_id)
            ->get();

        return view('pages.guru.absensi.edit', compact('absensi', 'siswas', 'kelas', 'mapels'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alfa',
            'pertemuan' => 'nullable|integer|min:1|max:100' // New validation for pertemuan
        ]);

        $absensi = Absensi::findOrFail($id);

        // Update the record and set the teacher edited flag
        $absensi->update([
            'siswa_id' => $request->siswa_id,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'tanggal' => $request->tanggal,
            'status' => $request->status,
            'pertemuan' => $request->pertemuan, // New field
            'is_teacher_edited' => true, // Set teacher edited flag
            'is_student_submitted' => $absensi->is_student_submitted // Preserve student submitted flag
        ]);

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $absensi = Absensi::findOrFail($id);
        $absensi->delete();

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil dihapus');
    }

    // New Methods for Enhanced Functionality

    public function bulkEntry(Request $request)
    {
        $guru = Auth::user()->guru;
        $jadwals = $guru ? $guru->jadwal()->with('kelas', 'mapel')->get() : collect();

        return view('pages.guru.absensi.bulk-entry', compact('jadwals'));
    }

    public function getStudentsByClass(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'tanggal' => 'required|date'
        ]);

        $guru = Auth::user()->guru;
        $siswas = Siswa::where('kelas_id', $request->kelas_id)
            ->orderBy('nama')
            ->get();

        // Get existing attendance
        $existingAttendance = Absensi::where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('tanggal', $request->tanggal)
            ->where('guru_id', $guru->id)
            ->pluck('status', 'siswa_id')
            ->toArray();

        return response()->json([
            'students' => $siswas,
            'existing_attendance' => $existingAttendance
        ]);
    }

    public function statistics(Request $request)
    {
        $guru = Auth::user()->guru;
        $kelas = Kelas::all();
        $mapel = Mapel::all();

        $selectedKelas = $request->input('kelas_id');
        $selectedMapel = $request->input('mapel_id');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $statistics = [];

        if ($selectedKelas && $selectedMapel) {
            $siswas = Siswa::where('kelas_id', $selectedKelas)->get();

            foreach ($siswas as $siswa) {
                // Get all master absensi records for this class, subject, and period
                $masterAbsensis = \App\Models\MasterAbsensi::where('kelas_id', $selectedKelas)
                    ->where('mapel_id', $selectedMapel)
                    ->where('guru_id', $guru->id)
                    ->whereMonth('tanggal', $month)
                    ->whereYear('tanggal', $year)
                    ->get();

                // Get student's attendance records for these master records
                $masterIds = $masterAbsensis->pluck('id');
                $studentAttendances = AbsensiSiswa::where('siswa_id', $siswa->id)
                    ->whereIn('master_absensi_id', $masterIds)
                    ->get();

                $totalDays = $masterAbsensis->count();
                $presentDays = $studentAttendances->where('status', 'hadir')->count();

                $statistics[] = [
                    'siswa' => $siswa,
                    'total_days' => $totalDays,
                    'present_days' => $presentDays,
                    'attendance_rate' => $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0
                ];
            }
        }

        return view('pages.guru.absensi.statistics', compact(
            'kelas',
            'mapel',
            'statistics',
            'selectedKelas',
            'selectedMapel',
            'month',
            'year'
        ));
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $guru = Auth::user()->guru;
        $kelas = Kelas::find($request->kelas_id);
        $mapel = Mapel::find($request->mapel_id);

        $absensis = Absensi::with(['siswa'])
            ->where('guru_id', $guru->id)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->whereBetween('tanggal', [$request->start_date, $request->end_date])
            ->orderBy('tanggal')
            ->orderBy('siswa_id')
            ->get();

        $filename = "absensi_{$kelas->nama}_{$mapel->nama}_" . date('Y-m-d') . '.xlsx';

        return Excel::download(new AttendanceExport($absensis), $filename);
    }
}
