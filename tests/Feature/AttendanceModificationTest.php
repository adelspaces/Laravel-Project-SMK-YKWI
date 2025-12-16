<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Jurusan;
use App\Models\Jadwal;

class AttendanceModificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function student_can_submit_self_attendance()
    {
        // Create required related records
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Test Jurusan'
        ]);

        $mapel = Mapel::create([
            'nama_mapel' => 'Mathematics',
            'jurusan_id' => $jurusan->id
        ]);

        // Create a teacher user
        $teacherUser = User::create([
            'name' => 'Teacher Test',
            'email' => 'teacher@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru'
        ]);

        $guru = Guru::create([
            'user_id' => $teacherUser->id,
            'nama' => 'Teacher Test',
            'nip' => '1234567890',
            'mapel_id' => $mapel->id,
            'no_telp' => '081234567890',
            'alamat' => 'Test Address'
        ]);

        // Create a student user
        $studentUser = User::create([
            'name' => 'Student Test',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'nis' => '1234567890' // Add NIS to user
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'Class Test',
            'jurusan_id' => $jurusan->id,
            'guru_id' => $guru->id
        ]);

        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'kelas_id' => $kelas->id,
            'nama' => 'Student Test',
            'nis' => '1234567890', // Same NIS as user
            'telp' => '081234567890',
            'alamat' => 'Test Address'
        ]);

        // Create a schedule
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

        $jadwal = Jadwal::create([
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'guru_id' => $guru->id, // Add guru_id
            'hari' => $dayMapping[now()->format('l')], // Today's day name in Indonesian
            'dari_jam' => '08:00:00',
            'sampai_jam' => '10:00:00'
        ]);

        // Acting as student
        $response = $this->actingAs($studentUser);

        // First, visit the self-attendance page to make sure it's accessible
        $response = $this->get(route('siswa.absensi.self'));
        $response->assertStatus(200);

        // Submit attendance
        $response = $this->post(route('siswa.absensi.storeSelf'), [
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tanggal' => now()->format('Y-m-d'),
            'status' => 'hadir',
            'pertemuan' => 1
        ]);

        // Debug: Print session data
        // dump(session()->all());

        // Check if there's an error in the session
        if ($response->getSession()->has('error')) {
            // dump('Error: ' . $response->getSession()->get('error'));
        }

        // Check the response
        $response->assertSessionHas('success');

        // Check that attendance was recorded with student submission flag
        $this->assertDatabaseHas('absensis', [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'status' => 'hadir',
            'pertemuan' => 1,
            'is_student_submitted' => true
        ]);
    }

    /** @test */
    public function teacher_can_edit_student_submitted_attendance()
    {
        // Create required related records
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Test Jurusan 2'
        ]);

        $mapel = Mapel::create([
            'nama_mapel' => 'Science',
            'jurusan_id' => $jurusan->id
        ]);

        // Create a teacher user
        $teacherUser = User::create([
            'name' => 'Teacher Test 2',
            'email' => 'teacher2@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru'
        ]);

        $guru = Guru::create([
            'user_id' => $teacherUser->id,
            'nama' => 'Teacher Test 2',
            'nip' => '1234567891',
            'mapel_id' => $mapel->id,
            'no_telp' => '081234567891',
            'alamat' => 'Test Address 2'
        ]);

        // Create a student
        $kelas = Kelas::create([
            'nama_kelas' => 'Class Test 2',
            'jurusan_id' => $jurusan->id,
            'guru_id' => $guru->id
        ]);

        $studentUser = User::create([
            'name' => 'Student Test 2',
            'email' => 'student2@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'nis' => '1234567891' // Add NIS to user
        ]);

        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'kelas_id' => $kelas->id,
            'nama' => 'Student Test 2',
            'nis' => '1234567891', // Same NIS as user
            'telp' => '081234567891',
            'alamat' => 'Test Address 2'
        ]);

        // Create student-submitted attendance
        $absensi = Absensi::create([
            'guru_id' => $guru->id,
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tanggal' => now(),
            'status' => 'hadir',
            'pertemuan' => 1,
            'is_student_submitted' => true
        ]);

        // Acting as teacher
        $response = $this->actingAs($teacherUser);

        // Edit the attendance
        $response = $this->put(route('absensi.update', $absensi->id), [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tanggal' => now()->format('Y-m-d'),
            'status' => 'izin',
            'pertemuan' => 2
        ]);

        $response->assertRedirect(route('absensi.index'));
        $response->assertSessionHas('success');

        // Check that attendance was updated with teacher edited flag
        $this->assertDatabaseHas('absensis', [
            'id' => $absensi->id,
            'status' => 'izin',
            'pertemuan' => 2,
            'is_student_submitted' => true,
            'is_teacher_edited' => true
        ]);
    }

    /** @test */
    public function attendance_records_show_pertemuan_number()
    {
        // Create required related records
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Test Jurusan 3'
        ]);

        $mapel = Mapel::create([
            'nama_mapel' => 'English',
            'jurusan_id' => $jurusan->id
        ]);

        // Create a teacher user
        $teacherUser = User::create([
            'name' => 'Teacher Test 3',
            'email' => 'teacher3@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru'
        ]);

        $guru = Guru::create([
            'user_id' => $teacherUser->id,
            'nama' => 'Teacher Test 3',
            'nip' => '1234567892',
            'mapel_id' => $mapel->id,
            'no_telp' => '081234567892',
            'alamat' => 'Test Address 3'
        ]);

        // Create a student user
        $studentUser = User::create([
            'name' => 'Student Test 3',
            'email' => 'student3@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'nis' => '1234567892' // Add NIS to user
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'Class Test 3',
            'jurusan_id' => $jurusan->id,
            'guru_id' => $guru->id
        ]);

        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'kelas_id' => $kelas->id,
            'nama' => 'Student Test 3',
            'nis' => '1234567892', // Same NIS as user
            'telp' => '081234567892',
            'alamat' => 'Test Address 3'
        ]);

        // Create attendance with pertemuan number
        $absensi = Absensi::create([
            'guru_id' => $guru->id,
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tanggal' => now(),
            'status' => 'hadir',
            'pertemuan' => 3
        ]);

        // Acting as student
        $response = $this->actingAs($studentUser);

        // Visit attendance index page
        $response = $this->get(route('siswa.absensi.index'));

        // Check that pertemuan number is displayed
        $response->assertSee('3');
    }
}
