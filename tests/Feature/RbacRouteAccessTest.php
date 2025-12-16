<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class RbacRouteAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run the seeders to populate the database
        Artisan::call('db:seed');
    }

    /** @test */
    public function admin_user_can_access_admin_routes()
    {
        // Get the admin user
        $admin = User::where('email', 'admin@mail.com')->first();

        // Acting as admin user
        $this->actingAs($admin);

        // Test access to admin dashboard
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);

        // Test access to admin resource routes
        $response = $this->get('/jurusan');
        $response->assertStatus(200);

        $response = $this->get('/mapel');
        $response->assertStatus(200);

        $response = $this->get('/guru');
        $response->assertStatus(200);

        $response = $this->get('/kelas');
        $response->assertStatus(200);

        $response = $this->get('/siswa');
        $response->assertStatus(200);

        $response = $this->get('/user');
        $response->assertStatus(200);

        $response = $this->get('/jadwal');
        $response->assertStatus(200);

        $response = $this->get('/pengumuman-sekolah');
        $response->assertStatus(200);

        $response = $this->get('/pengaturan');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_user_cannot_access_guru_routes()
    {
        // Get the admin user
        $admin = User::where('email', 'admin@mail.com')->first();

        // Acting as admin user
        $this->actingAs($admin);

        // Test access to guru dashboard (should be denied)
        $response = $this->get('/guru/dashboard');
        $response->assertStatus(302); // Redirected due to unauthorized access
    }

    /** @test */
    public function guru_user_can_access_guru_routes()
    {
        // Get the guru user
        $guru = User::where('email', 'budi@mail.com')->first();

        // Acting as guru user
        $this->actingAs($guru);

        // Test access to guru dashboard
        $response = $this->get('/guru/dashboard');
        $response->assertStatus(200);

        // Test access to guru resource routes
        $response = $this->get('/materi');
        $response->assertStatus(200);

        $response = $this->get('/tugas');
        $response->assertStatus(200);
    }

    /** @test */
    public function guru_user_cannot_access_admin_routes()
    {
        // Get the guru user
        $guru = User::where('email', 'budi@mail.com')->first();

        // Acting as guru user
        $this->actingAs($guru);

        // Test access to admin dashboard (should be denied)
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302); // Redirected due to unauthorized access
    }

    /** @test */
    public function siswa_user_can_access_siswa_routes()
    {
        // Get the siswa user
        $siswa = User::where('email', 'kevin@mail.com')->first();

        // Acting as siswa user
        $this->actingAs($siswa);

        // Test access to siswa dashboard
        $response = $this->get('/siswa/dashboard');
        $response->assertStatus(200);

        // Test access to siswa routes
        $response = $this->get('/siswa/materi');
        $response->assertStatus(200);

        $response = $this->get('/siswa/tugas');
        $response->assertStatus(200);
    }

    /** @test */
    public function siswa_user_cannot_access_guru_routes()
    {
        // Get the siswa user
        $siswa = User::where('email', 'kevin@mail.com')->first();

        // Acting as siswa user
        $this->actingAs($siswa);

        // Test access to guru dashboard (should be denied)
        $response = $this->get('/guru/dashboard');
        $response->assertStatus(302); // Redirected due to unauthorized access
    }
}
