@extends('layouts.main')
@section('title', 'Edit Absensi')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit Absensi</h4>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')

                            <form action="{{ route('absensi.update', $absensi->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="siswa_id">Siswa</label>
                                    <select name="siswa_id" id="siswa_id" class="form-control" required>
                                        <option value="">-- Pilih Siswa --</option>
                                        @foreach ($siswas as $siswa)
                                            <option value="{{ $siswa->id }}" {{ $absensi->siswa_id == $siswa->id ? 'selected' : '' }}>
                                                {{ $siswa->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="kelas_id">Kelas</label>
                                    <select name="kelas_id" id="kelas_id" class="form-control" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}" {{ $absensi->kelas_id == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="mapel_id">Mata Pelajaran</label>
                                    <select name="mapel_id" id="mapel_id" class="form-control" required>
                                        <option value="">-- Pilih Mapel --</option>
                                        @foreach ($mapels as $mapel)
                                            <option value="{{ $mapel->id }}" {{ $absensi->mapel_id == $mapel->id ? 'selected' : '' }}>
                                                {{ $mapel->nama_mapel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control"
                                           value="{{ old('tanggal', $absensi->tanggal->format('Y-m-d')) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status Kehadiran</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="hadir" {{ $absensi->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                        <option value="izin" {{ $absensi->status == 'izin' ? 'selected' : '' }}>Izin</option>
                                        <option value="sakit" {{ $absensi->status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                        <option value="alfa" {{ $absensi->status == 'alfa' ? 'selected' : '' }}>Alfa</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="pertemuan">Pertemuan Ke-</label>
                                    <select name="pertemuan" id="pertemuan" class="form-control">
                                        <option value="">-- Pilih Pertemuan --</option>
                                        @for($i = 1; $i <= 20; $i++)
                                            <option value="{{ $i }}" {{ $absensi->pertemuan == $i ? 'selected' : '' }}>
                                                Pertemuan {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <!-- Display submission information -->
                                @if($absensi->isStudentSubmitted())
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Informasi:</strong> Absensi ini awalnya diinput oleh siswa.
                                        @if($absensi->isTeacherEdited())
                                            <br><strong>Diedit oleh guru.</strong>
                                        @endif
                                    </div>
                                @endif

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Absensi
                                </button>
                                <a href="{{ route('absensi.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
