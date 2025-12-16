<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterSiswaController extends Controller
{
    public function showRegistrationForm()
    {
        $kelas = \App\Models\Kelas::all();
        return view('auth.register-siswa', compact('kelas'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'no_telp' => ['required', 'string'],
            'alamat' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:10', 'unique:siswas'],
            'kelas_id' => ['required', 'exists:kelas,id'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Simpan ke users
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
            'nis' => $request->nis,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
        ]);

        // Simpan ke tabel siswa
        Siswa::create([
            'nis' => $request->nis,
            'nama' => $request->name,
            'telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'kelas_id' => $request->kelas_id,
        ]);

        Auth::login($user);
        return redirect('/siswa/dashboard');
    }
}
