@extends('layouts.main')
@section('title', 'Import Soal Excel')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Import Soal dari Excel</h4>
                            <a href="{{ route('banksoal.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-8">
                                    <form action="{{ route('banksoal.preview_import') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        
                                        <div class="form-group">
                                            <label for="mapel_id">Mata Pelajaran</label>
                                            <select name="mapel_id" class="form-control" required>
                                                <option value="">-- Pilih Mata Pelajaran --</option>
                                                @foreach ($mapels as $mapel)
                                                    <option value="{{ $mapel->id }}" {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}>
                                                        {{ $mapel->nama_mapel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('mapel_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="file">File Excel</label>
                                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                                            <small class="form-text text-muted">
                                                Format yang didukung: .xlsx, .xls, .csv (Maksimal 5MB)
                                            </small>
                                            @error('file')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-eye"></i> Preview Import
                                            </button>
                                            <a href="{{ route('banksoal.download_template') }}" class="btn btn-info ml-2">
                                                <i class="fas fa-download"></i> Download Template
                                            </a>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Panduan Import</h5>
                                        </div>
                                        <div class="card-body">
                                            <ol>
                                                <li>Download template Excel terlebih dahulu</li>
                                                <li>Isi data soal sesuai format template</li>
                                                <li>Pastikan tipe soal diisi dengan benar:
                                                    <ul>
                                                        <li><code>pilihan_ganda</code></li>
                                                        <li><code>esai</code></li>
                                                        <li><code>benar_salah</code></li>
                                                    </ul>
                                                </li>
                                                <li>Upload file dan preview sebelum import</li>
                                                <li>Konfirmasi import jika data sudah benar</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection