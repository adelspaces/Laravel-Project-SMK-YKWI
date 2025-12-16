@extends('layouts.auth')

@section('title', 'Register Guru')

@section('content')
    <div class="container mt-5">
        <h2>Daftar Sebagai Guru</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.guru') }}">
            @csrf

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label>NIP</label>
                <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" required>
            </div>

            <div class="form-group">
                <label>No Telepon</label>
                <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp') }}" required>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" required>{{ old('alamat') }}</textarea>
            </div>

            <div class="form-group">
                <label>Jurusan</label>
                <select name="jurusan_id" class="form-control" required>
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach ($jurusans as $j)
                        <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama_jurusan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Mata Pelajaran</label>
                <select name="mapel_id" class="form-control" required>
                    <option value="">-- Pilih Mapel --</option>
                    @foreach ($mapel as $m)
                        <option value="{{ $m->id }}" {{ old('mapel_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Daftar</button>
        </form>
    </div>
@endsection
