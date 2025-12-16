@extends('layouts.main')
@section('title', 'Tambah Tugas')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Tambah Tugas</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('tugas.index') }}">Tugas</a></div>
                <div class="breadcrumb-item active">Tambah Tugas</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Form Tambah Tugas</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('tugas.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                {{-- Pilih Kelas --}}
                                <div class="form-group">
                                    <label for="kelas_id">Kelas</label>
                                    <select name="kelas_id" id="kelas_id" class="form-control" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Judul Tugas --}}
                                <div class="form-group">
                                    <label for="judul">Judul Tugas</label>
                                    <input type="text" id="judul" name="judul"
                                        class="form-control @error('judul') is-invalid @enderror"
                                        placeholder="Masukkan judul tugas" required>
                                    @error('judul')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Deskripsi --}}
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi</label>
                                    <textarea id="deskripsi" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror"
                                        placeholder="Masukkan deskripsi tugas"></textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- File Tugas --}}
                                <div class="form-group">
                                    <label for="file">File Tugas</label>
                                    <input type="file" id="file" name="file"
                                        class="form-control @error('file') is-invalid @enderror">
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('tugas.index') }}" class="btn btn-secondary">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
