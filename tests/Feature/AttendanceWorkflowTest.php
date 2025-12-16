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

class AttendanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function complete_attendance_workflow_works_correctly()
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
            'nis' => '1234567890'
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
            'nis' => '1234567890',
            'telp' => '081234567890',
            'alamat' => 'Test Address'
        ]);

        // Create a schedule
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
            'guru_id' => $guru->id,
            'hari' => $dayMapping[now()->format('l')],
            'dari_jam' => '08:00:00',
            'sampai_jam' => '10:00:00'
        ]);

        // Step 1: Teacher creates attendance record
        $response = $this->actingAs($teacherUser);
        
        $response = $this->post(route('absensi.store'), [
            'tanggal' => now()->format('Y-m-d'),
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'absen' => [
                $siswa->id => 'alfa'
            ],
            'pertemuan' => 1
        ]);

        $response->assertSessionHas('success');
        
        // Check that attendance was recorded by teacher
        $this->assertDatabaseHas('absensis', [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'status' => 'alfa',
            'pertemuan' => 1,
            'is_student_submitted' => false,
            'is_teacher_edited' => false // Should be false when teacher creates attendance
        ]);

        // Step 2: Student checks and submits attendance
        $response = $this->actingAs($studentUser);
        
        // Visit self-attendance page
        $response = $this->get(route('siswa.absensi.self'));
        $response->assertStatus(200);

        // Submit attendance to change status
        $response = $this->post(route('siswa.absensi.storeSelf'), [
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tanggal' => now()->format('Y-m-d'),
            'status' => 'izin',
            'pertemuan' => 1
        ]);

        $response->assertSessionHas('success');
        
        // Check that attendance was updated with student submission flag
        $this->assertDatabaseHas('absensis', [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'status' => 'izin',
            'pertemuan' => 1,
            'is_student_submitted' => true,
            'is_teacher_edited' => false // Should be false initially when student submits
        ]);

        // Step 3: Teacher validates by editing the attendance
        $response = $this->actingAs($teacherUser);
        
        // Get the attendance record
        $absensi = Absensi::where('siswa_id', $siswa->id)
            ->where('kelas_id', $kelas->id)
            ->where('mapel_id', $mapel->id)
            ->where('tanggal', now()->format('Y-m-d'))
            ->first();
            
        // Edit the attendance
        $response = $this->put(route('absensi.update', $absensi->id), [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tanggal' => now()->format('Y-m-d'),
            'status' => 'hadir',
            'pertemuan' => 1
        ]);

        $response->assertRedirect(route('absensi.index'));
        $response->assertSessionHas('success');

        // Check that attendance was updated with teacher edited flag
        $this->assertDatabaseHas('absensis', [
            'id' => $absensi->id,
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'status' => 'hadir',
            'pertemuan' => 1,
            'is_student_submitted' => true,
            'is_teacher_edited' => true
        ]);
    }

    /** @test */
    public function student_cannot_change_attendance_after_teacher_edits()
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

        // Create a student user
        $studentUser = User::create([
            'name' => 'Student Test 2',
            'email' => 'student2@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'nis' => '1234567891'
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'Class Test 2',
            'jurusan_id' => $jurusan->id,
            'guru_id' => $guru->id
        ]);

        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'kelas_id' => $kelas->id,
            'nama' => 'Student Test 2',
            'nis' => '1234567891',
            'telp' => '081234567891',
            'alamat' => 'Test Address 2'
        ]);

        // Create a schedule
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
            'guru_id' => $guru->id,
            'hari' => $dayMapping[now()->format('l')],
            'dari_jam' => '08:00:00',
            'sampai_jam' => '10:00:00'
        ]);

        // Acting as student to submit attendance
        $response = $this->actingAs($studentUser);
        
        $response = $this->post(route('siswa.absensi.storeSelf'), [
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tanggal' => now()->format('Y-m-d'),
            'status' => 'izin',
            'pertemuan' => 1
        ]);

        $response->assertSessionHas('success');

        // Acting as teacher to edit attendance
        $response = $this->actingAs($teacherUser);
        
        // Get the attendance record
        $absensi = Absensi::where('siswa_id', $siswa->id)
            ->where('kelas_id', $kelas->id)
            ->where('mapel_id', $mapel->id)
            ->where('tanggal', now()->format('Y-m-d'))
            ->first();
            
        // Edit the attendance
        $response = $this->put(route('absensi.update', $absensi->id), [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tanggal' => now()->format('Y-m-d'),
            'status' => 'hadir',
            'pertemuan' => 1
        ]);

        $response->assertRedirect(route('absensi.index'));
        $response->assertSessionHas('success');

        // Acting as student again to try to change attendance
        $response = $this->actingAs($studentUser);
        
        // Try to submit attendance again
        $response = $this->post(route('siswa.absensi.storeSelf'), [
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tanggal' => now()->format('Y-m-d'),
            'status' => 'sakit',
            'pertemuan' => 1
        ]);

        // Should get an error
        $response->assertSessionHas('error');
        $this->assertEquals('Absensi ini sudah diedit oleh guru dan tidak dapat diubah.', session('error'));
        
        // Check that attendance was NOT updated
        $this->assertDatabaseHas('absensis', [
            'id' => $absensi->id,
            'status' => 'hadir', // Should still be 'hadir' from teacher's edit
            'is_student_submitted' => true,
            'is_teacher_edited' => true
        ]);
    }
}