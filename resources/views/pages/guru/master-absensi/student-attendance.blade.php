@extends('layouts.main')
@section('title', 'Absensi Siswa')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Absensi Siswa</h4>
                            <a href="{{ route('absensi.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')

                            <!-- Master Absensi Info -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5>Informasi Master Absensi</h5>
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Tanggal:</strong></td>
                                                    <td>{{ $masterAbsensi->tanggal->format('d/m/Y') }}</td>
                                                    <td><strong>Kelas:</strong></td>
                                                    <td>{{ $masterAbsensi->kelas->nama_kelas ?? '-' }}{{ $masterAbsensi->kelas->jurusan ? ' - ' . $masterAbsensi->kelas->jurusan->nama_jurusan : '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Mata Pelajaran:</strong></td>
                                                    <td>{{ $masterAbsensi->mapel->nama_mapel ?? '-' }}</td>
                                                    <td><strong>Pertemuan:</strong></td>
                                                    <td>{{ $masterAbsensi->pertemuan }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Guru:</strong></td>
                                                    <td>{{ $masterAbsensi->guru->nama ?? '-' }}</td>
                                                    <td><strong>Jumlah Siswa:</strong></td>
                                                    <td>{{ $masterAbsensi->absensiSiswa->count() }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($masterAbsensi->absensiSiswa->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-2">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Siswa</th>
                                                <th>Status</th>
                                                <th>Validasi Guru</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($masterAbsensi->absensiSiswa as $absen)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $absen->siswa->nama ?? '-' }}</td>
                                                    <td>
                                                        @if($absen->status == 'hadir')
                                                            <span class="badge badge-success">Hadir</span>
                                                        @elseif($absen->status == 'izin')
                                                            <span class="badge badge-warning">Izin</span>
                                                        @elseif($absen->status == 'sakit')
                                                            <span class="badge badge-info">Sakit</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($absen->is_teacher_validated)
                                                            <span class="badge badge-success">Tervalidasi</span>
                                                        @else
                                                            <span class="badge badge-secondary">Belum Divalidasi</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(!$absen->is_teacher_validated)
                                                            <a href="{{ route('absensi.master.edit-student-attendance', $absen->id) }}"
                                                               class="btn btn-warning btn-sm">
                                                                <i class="fas fa-edit"></i> Validasi
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Sudah Divalidasi</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Belum ada siswa yang mengisi absensi untuk pertemuan ini.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
