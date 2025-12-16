<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run the seeders in the correct order to populate the database
        Artisan::call('db:seed', ['--class' => 'JurusanSeeder']);
        Artisan::call('db:seed', ['--class' => 'MapelSeeder']);
        Artisan::call('db:seed', ['--class' => 'GuruSeeder']);
        Artisan::call('db:seed', ['--class' => 'KelasSeeder']);
        Artisan::call('db:seed', ['--class' => 'SiswaSeeder']);
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
        Artisan::call('db:seed', ['--class' => 'PengaturanSeeder']);
    }

    /** @test */
    public function admin_user_can_access_admin_dashboard()
    {
        // Get the admin user
        $admin = User::where('email', 'admin@mail.com')->first();

        // Acting as admin user
        $this->actingAs($admin);

        // Visit admin dashboard
        $response = $this->get('/admin/dashboard');

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
    }

    /** @test */
    public function guru_user_can_access_guru_dashboard()
    {
        // Get the guru user
        $guru = User::where('email', 'budi@mail.com')->first();

        // Acting as guru user
        $this->actingAs($guru);

        // Visit guru dashboard
        $response = $this->get('/guru/dashboard');

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
    }

    /** @test */
    public function siswa_user_can_access_siswa_dashboard()
    {
        // Get the siswa user
        $siswa = User::where('email', 'kevin@mail.com')->first();

        // Acting as siswa user
        $this->actingAs($siswa);

        // Visit siswa dashboard
        $response = $this->get('/siswa/dashboard');

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
    }
}
