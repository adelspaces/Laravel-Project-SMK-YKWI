<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $siswa = Siswa::OrderBy('nama', 'asc')->get();
        $kelas = Kelas::all();
        return view('pages.admin.siswa.index', compact('siswa', 'kelas'));
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
            'nis' => 'required|unique:siswas',
            'telp' => 'required',
            'alamat' => 'required',
            'kelas_id' => 'required|unique:siswas',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'nis.unique' => 'NIS sudah terdaftar',
            'kelas_id.unique' => 'Siswa sudah terdaftar di kelas ini',
        ]);

        if (isset($request->foto)) {
            $file = $request->file('foto');
            $namaFoto = time() . '.' . $file->getClientOriginalExtension();
            $foto = $file->storeAs('images/siswa', $namaFoto, 'public');
        }

        $siswa = new Siswa;
        $siswa->nama = $request->nama;
        $siswa->nis = $request->nis;
        $siswa->telp = $request->telp;
        $siswa->alamat = $request->alamat;
        $siswa->kelas_id = $request->kelas_id;
        $siswa->foto = $foto;
        $siswa->save();


        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan');
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
        $siswa = Siswa::findOrFail($id);

        return view('pages.admin.siswa.profile', compact('siswa'));
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
        $kelas = Kelas::all();
        $siswa = Siswa::findOrFail($id);

        return view('pages.admin.siswa.edit', compact('siswa', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Siswa $siswa)
    {
        if ($request->nis != $siswa->nis) {
            $this->validate($request, [
                'nis' => 'unique:siswas'
            ], [
                'nis.unique' => 'NIS sudah terdaftar',
            ]);
        }

        $siswa->nama = $request->nama;
        $siswa->nis = $request->nis;
        $siswa->telp = $request->telp;
        $siswa->alamat = $request->alamat;
        $siswa->kelas_id = $request->kelas_id;

        if ($request->hasFile('foto')) {
            $lokasi = 'img/siswa/' . $siswa->foto;
            if (File::exists($lokasi)) {
                File::delete($lokasi);
            }
            $foto = $request->file('foto');
            $namaFoto = time() . '.' . $foto->getClientOriginalExtension();
            $tujuanFoto = public_path('/img/siswa');
            $foto->move($tujuanFoto, $namaFoto);
            $siswa->foto = $namaFoto;
        }

        $siswa->update();

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $siswa = Siswa::find($id);
        $lokasi = 'img/siswa/' . $siswa->foto;
        if (File::exists($lokasi)) {
            File::delete($lokasi);
        }

        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil dihapus');
    }

    /**
     * Display attendance records for the authenticated student only.
     *
     * @return \Illuminate\Http\Response
     */
    public function absensi()
    {
        // Get the authenticated user's student record
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get attendance records for this student only
        $absensis = Absensi::with(['kelas', 'mapel', 'guru'])
            ->where('siswa_id', $siswa->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('pages.siswa.absensi.index', compact('absensis', 'siswa'));
    }

    /**
     * Display the list of master attendance records for the authenticated student.
     *
     * @return \Illuminate\Http\Response
     */
    public function absensiList()
    {
        // Get the authenticated user's student record
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get master attendance records (without student data) for this student's class
        // These are records where siswa_id is null and the class matches
        $masterAbsensis = Absensi::with(['kelas', 'mapel', 'guru'])
            ->whereNull('siswa_id')
            ->where('kelas_id', $siswa->kelas_id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Get student's own attendance records (to show current status)
        $studentAbsensis = Absensi::with(['kelas', 'mapel', 'guru'])
            ->where('siswa_id', $siswa->id)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->keyBy(function ($item) {
                return $item->kelas_id . '_' . $item->mapel_id . '_' . $item->tanggal->format('Y-m-d') . '_' . $item->pertemuan;
            });

        // Combine master records with student's attendance status
        $absensis = $masterAbsensis->map(function ($master) use ($studentAbsensis, $siswa) {
            $key = $master->kelas_id . '_' . $master->mapel_id . '_' . $master->tanggal->format('Y-m-d') . '_' . $master->pertemuan;

            if ($studentAbsensis->has($key)) {
                // Student has already submitted attendance for this master record
                $studentRecord = $studentAbsensis->get($key);
                $master->student_status = $studentRecord->status;
                $master->student_id = $studentRecord->id;
                $master->is_student_submitted = $studentRecord->is_student_submitted;
                $master->is_teacher_edited = $studentRecord->is_teacher_edited;
            } else {
                // Student hasn't submitted attendance yet
                $master->student_status = null;
                $master->student_id = null;
                $master->is_student_submitted = false;
                $master->is_teacher_edited = false;
            }

            return $master;
        });

        return view('pages.siswa.absensi.list', compact('absensis', 'siswa'));
    }

    /**
     * Update student's attendance status for a master attendance record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAttendance(Request $request, $id)
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return response()->json(['error' => 'Data siswa tidak ditemukan.'], 404);
        }

        // Validate request
        $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alfa'
        ]);

        try {
            // Find the master attendance record
            $masterAbsensi = Absensi::where('id', $id)
                ->whereNull('siswa_id') // It's a master record
                ->first();

            if (!$masterAbsensi) {
                return response()->json(['error' => 'Data absensi master tidak ditemukan.'], 404);
            }

            // Check if student has already submitted attendance for this master record
            $existingStudentRecord = Absensi::where('siswa_id', $siswa->id)
                ->where('kelas_id', $masterAbsensi->kelas_id)
                ->where('mapel_id', $masterAbsensi->mapel_id)
                ->where('tanggal', $masterAbsensi->tanggal)
                ->where('pertemuan', $masterAbsensi->pertemuan)
                ->first();

            if ($existingStudentRecord) {
                // If record exists and was edited by teacher, prevent changes
                if ($existingStudentRecord->isTeacherEdited()) {
                    return response()->json(['error' => 'Absensi ini sudah diedit oleh guru dan tidak dapat diubah.'], 403);
                }

                // Update existing record
                $existingStudentRecord->update([
                    'status' => $request->status,
                    'is_student_submitted' => true,
                    'updated_at' => now()
                ]);

                return response()->json(['success' => 'Absensi berhasil diperbarui.']);
            } else {
                // Create new student attendance record based on master
                Absensi::create([
                    'guru_id' => $masterAbsensi->guru_id,
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $masterAbsensi->kelas_id,
                    'mapel_id' => $masterAbsensi->mapel_id,
                    'tanggal' => $masterAbsensi->tanggal,
                    'status' => $request->status,
                    'pertemuan' => $masterAbsensi->pertemuan,
                    'is_student_submitted' => true,
                    'is_teacher_edited' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                return response()->json(['success' => 'Absensi berhasil disimpan.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan absensi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the form for student to submit their own attendance.
     *
     * @return \Illuminate\Http\Response
     */
    public function selfAttendance()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get today's date
        $today = Carbon::today();

        // Convert English day names to Indonesian
        $dayMapping = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        $indonesianDay = $dayMapping[$today->format('l')];

        // Get classes scheduled for this student today
        $jadwals = Jadwal::with(['kelas', 'mapel', 'guru'])
            ->where('kelas_id', $siswa->kelas_id)
            ->where('hari', $indonesianDay) // Using Indonesian day name
            ->get();

        return view('pages.siswa.absensi.self-attendance', compact('jadwals', 'siswa', 'today'));
    }

    /**
     * Store student's self-submitted attendance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeSelfAttendance(Request $request)
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // Validate request
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'tanggal' => 'required|date|before_or_equal:today',
            'status' => 'required|in:hadir,izin,sakit,alfa',
            'pertemuan' => 'required|integer|min:1|max:100'
        ]);

        // Check if student is enrolled in this class
        if ($siswa->kelas_id != $request->kelas_id) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar di kelas ini.');
        }

        try {
            DB::beginTransaction();

            // Check if attendance record already exists and was edited by teacher after student submission
            $existingAbsensi = Absensi::where('siswa_id', $siswa->id)
                ->where('kelas_id', $request->kelas_id)
                ->where('mapel_id', $request->mapel_id)
                ->where('tanggal', $request->tanggal)
                ->first();

            // Only prevent changes if it was originally submitted by student and then edited by teacher
            if ($existingAbsensi && $existingAbsensi->isStudentSubmitted() && $existingAbsensi->isTeacherEdited()) {
                DB::rollback();
                return redirect()->back()->with('error', 'Absensi ini sudah diedit oleh guru dan tidak dapat diubah.');
            }

            // Convert English day names to Indonesian
            $dayMapping = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            ];

            $indonesianDay = $dayMapping[Carbon::parse($request->tanggal)->format('l')];

            // Find the schedule to get the guru_id
            $jadwal = Jadwal::where('kelas_id', $request->kelas_id)
                ->where('mapel_id', $request->mapel_id)
                ->where('hari', $indonesianDay)
                ->first();

            if (!$jadwal || !$jadwal->guru_id) {
                DB::rollback();
                return redirect()->back()->with('error', 'Jadwal tidak ditemukan atau tidak ada guru yang mengajar.');
            }

            // Create or update attendance record with student submission flag
            $absensi = Absensi::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $request->kelas_id,
                    'mapel_id' => $request->mapel_id,
                    'tanggal' => $request->tanggal
                ],
                [
                    'guru_id' => $jadwal->guru_id, // Set the guru_id from the schedule
                    'status' => $request->status,
                    'pertemuan' => $request->pertemuan,
                    'is_student_submitted' => true,
                    'updated_at' => now()
                ]
            );

            // If this is a new record, set created_at
            if ($absensi->wasRecentlyCreated) {
                $absensi->created_at = now();
                $absensi->save();
            } else {
                // If updating existing record, preserve the is_student_submitted flag if it was already set
                if (!$absensi->isStudentSubmitted()) {
                    $absensi->is_student_submitted = true;
                    $absensi->save();
                }
            }

            DB::commit();

            return redirect()->route('siswa.absensi.index')->with('success', 'Absensi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    /**
     * Check if student has already submitted attendance for a class/date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkAttendanceStatus(Request $request)
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return response()->json(['error' => 'Data siswa tidak ditemukan.'], 404);
        }

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'tanggal' => 'required|date'
        ]);

        $exists = Absensi::where('siswa_id', $siswa->id)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('tanggal', $request->tanggal)
            ->exists();

        return response()->json(['submitted' => $exists]);
    }

    public function dashboard()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // Ambil hari ini (paksa kapital awal biar sama persis dgn DB)
        $hariIni = ucfirst(strtolower(Carbon::now()->locale('id')->translatedFormat('l')));

        // Ambil jadwal sesuai kelas & hari ini
        $jadwal = Jadwal::with(['mapel', 'guru', 'kelas'])
            ->where('kelas_id', $siswa->kelas_id)
            ->where('hari', $hariIni)
            ->get();

        $pengumumans = DB::table('pengumumans')->latest()->get();
        $materi = DB::table('materis')->get();
        $tugas = DB::table('tugas')->get();

        return view('pages.siswa.dashboard', compact(
            'siswa',
            'jadwal',
            'hariIni',
            'pengumumans',
            'materi',
            'tugas'
        ));
    }
}
