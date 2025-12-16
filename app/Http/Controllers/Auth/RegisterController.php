<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    protected function redirectTo()
    {
        // Fixed: Using 'role' instead of 'roles' to match User model field
        switch ($this->guard()->user()->role) {
            case 'admin':
                return '/admin/dashboard';
            case 'guru':
                return '/guru/dashboard';
            case 'siswa':
                return '/siswa/dashboard';
            default:
                return '/home';
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string'], // Fixed: Changed from 'roles' to 'role'
            'no_telp' => ['required', 'string'],
            'alamat' => ['required', 'string', 'max:255'],
            'nis' => ['nullable', 'string', 'max:10', 'unique:siswas'],
            'nip' => ['nullable', 'string', 'max:10', 'unique:gurus'],
        ]);
    }

    protected function create(array $data)
    {
        // Simpan user ke tabel users
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'], // Fixed: Changed from 'roles' to 'role'
            'nis' => $data['role'] === 'siswa' ? $data['nis'] : null, // Fixed: Changed from 'roles' to 'role'
            'nip' => $data['role'] === 'guru' ? $data['nip'] : null, // Fixed: Changed from 'roles' to 'role'
            'no_telp' => $data['no_telp'],
            'alamat' => $data['alamat'],
        ]);

        // Simpan ke tabel siswa jika role siswa
        if ($data['role'] === 'siswa') { // Fixed: Changed from 'roles' to 'role'
            \App\Models\Siswa::create([
                'nis' => $data['nis'],
                'nama' => $data['name'],
                'telp' => $data['no_telp'],
                'alamat' => $data['alamat'],
                // 'kelas_id' => optional, jika kamu pakai
            ]);
        }

        // Simpan ke tabel guru jika role guru
        if ($data['role'] === 'guru') { // Fixed: Changed from 'roles' to 'role'
            \App\Models\Guru::create([
                'nip' => $data['nip'],
                'nama' => $data['name'],
                'no_telp' => $data['no_telp'],
                'alamat' => $data['alamat'],
                // 'mapel_id' => optional
            ]);
        }

        return $user;
    }
}
