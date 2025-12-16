<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Jurusan;

class RegisterGuruController extends Controller
{
    public function showRegistrationForm()
    {
        $mapel = Mapel::all();
        $jurusans = Jurusan::all();
        return view('auth.register-guru', compact('mapel', 'jurusans'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|string|min:8|confirmed',
            'nip'       => 'required|string|max:10|unique:gurus',
            'no_telp'   => 'required|string|max:15',
            'alamat'    => 'required|string|max:255',
            'mapel_id'  => 'required|exists:mapels,id',
            'jurusan_id' => 'required|exists:jurusans,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'    => 'guru',
            'nip'      => $request->nip,
            'no_telp'  => $request->no_telp,
            'alamat'   => $request->alamat,
        ]);

        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'nama' => $request->name,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'mapel_id' => $request->mapel_id,
            'jurusan_id' => $request->jurusan_id,
        ]);


        Auth::login($user);
        return redirect('/guru/dashboard');
    }
}
