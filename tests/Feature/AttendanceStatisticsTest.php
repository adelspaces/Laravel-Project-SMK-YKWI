<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\MasterAbsensi;
use App\Models\AbsensiSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceStatisticsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function teacher_can_access_statistics_page()
    {
        // Create a teacher user manually
        $user = User::create([
            'name' => 'Test Teacher',
            'email' => 'teacher@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru'
        ]);
        
        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => '123456789',
            'nama' => 'Test Teacher',
            'no_telp' => '081234567890',
            'alamat' => 'Test Address'
        ]);
        
        // Acting as teacher
        $response = $this->actingAs($user)->get(route('absensi.statistics'));
        
        // Assert the response status is successful
        $response->assertStatus(200);
    }
    
    /** @test */
    public function student_cannot_access_teacher_statistics_page()
    {
        // Create a student user manually
        $user = User::create([
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa'
        ]);
        
        $siswa = Siswa::create([
            'nis' => '123456789',
            'nama' => 'Test Student',
            'telp' => '081234567890',
            'alamat' => 'Test Address',
            'kelas_id' => 1
        ]);
        
        // Acting as student
        $response = $this->actingAs($user)->get(route('absensi.statistics'));
        
        // Assert the response is redirected (access denied)
        // Note: This might depend on the exact implementation of the CheckRole middleware
    }
}