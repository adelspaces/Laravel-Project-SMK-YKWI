@extends('layouts.main')
@section('title', 'Exam Result')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Hasil {{ ucfirst($result->kuisUjian->tipe) }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('siswa.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('siswa.ujian.index') }}">Ujian</a></div>
            <div class="breadcrumb-item">Hasil</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $result->kuisUjian->judul }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mata Pelajaran</label>
                                    <p>{{ $result->kuisUjian->mapel->nama_mapel }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Guru</label>
                                    <p>{{ $result->kuisUjian->guru->nama }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nilai</label>
                                    <h2>
                                        <span class="badge badge-{{ $result->nilai_total >= 70 ? 'success' : 'danger' }}">
                                            {{ number_format($result->nilai_total, 2) }}%
                                        </span>
                                    </h2>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Grade</label>
                                    <h2>
                                        <span class="badge badge-{{ $result->grade === 'A' || $result->grade === 'B' ? 'success' : ($result->grade === 'C' ? 'warning' : 'danger') }}">
                                            {{ $result->grade }}
                                        </span>
                                    </h2>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <h2>
                                        <span class="badge badge-{{ $result->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ $result->status === 'completed' ? 'Selesai' : 'Menunggu Penilaian' }}
                                        </span>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Statistik</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card card-statistic-1">
                                                <div class="card-icon bg-primary">
                                                    <i class="fas fa-question-circle"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4>Total Soal</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $result->total_soal }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card card-statistic-1">
                                                <div class="card-icon bg-success">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4>Benar</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $result->soal_benar }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card card-statistic-1">
                                                <div class="card-icon bg-danger">
                                                    <i class="fas fa-times-circle"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4>Salah</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $result->soal_salah }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card card-statistic-1">
                                                <div class="card-icon bg-warning">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4>Durasi</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $result->durasi_pengerjaan }} menit
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($result->status !== 'completed')
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Hasil ujian Anda belum selesai karena masih ada soal essay yang menunggu penilaian oleh guru.
                            </div>
                        @endif
                        
                        <div class="section-title">Detail Jawaban</div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="40%">Soal</th>
                                        <th width="20%">Jawaban Anda</th>
                                        <th width="15%">Tipe Soal</th>
                                        <th width="10%">Nilai</th>
                                        <th width="10%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kuisUjian->banksoals as $index => $banksoal)
                                        @php
                                            $jawaban = $jawabanDetails->get($banksoal->id);
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $banksoal->pertanyaan }}</td>
                                            <td>
                                                @if($jawaban)
                                                    {{ $jawaban->jawaban }}
                                                @else
                                                    <span class="text-muted">Tidak dijawab</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ in_array($banksoal->tipe_soal, ['pilihan_ganda', 'benar_salah']) ? 'primary' : 'info' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $banksoal->tipe_soal)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($jawaban)
                                                    @if(in_array($banksoal->tipe_soal, ['pilihan_ganda', 'benar_salah']))
                                                        {{ $banksoal->bobot_nilai ?? 10 }}
                                                    @else
                                                        @if($jawaban->status_penilaian === 'graded')
                                                            {{ $jawaban->nilai }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    @endif
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>
                                                @if($jawaban)
                                                    @if(in_array($banksoal->tipe_soal, ['pilihan_ganda', 'benar_salah']))
                                                        <span class="badge badge-success">Auto-graded</span>
                                                    @else
                                                        @if($jawaban->status_penilaian === 'graded')
                                                            <span class="badge badge-success">Graded</span>
                                                            @if($jawaban->feedback)
                                                                <br><small class="text-muted">Feedback: {{ $jawaban->feedback }}</small>
                                                            @endif
                                                        @else
                                                            <span class="badge badge-warning">Pending</span>
                                                        @endif
                                                    @endif
                                                @else
                                                    <span class="badge badge-secondary">Tidak dijawab</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Analisis Performa</h4>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Akurasi
                                                <span class="badge badge-primary badge-pill">{{ number_format($detailedAnalysis['accuracy'], 2) }}%</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Tingkat Penyelesaian
                                                <span class="badge badge-primary badge-pill">{{ number_format($detailedAnalysis['completion_rate'], 2) }}%</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Waktu per Soal
                                                <span class="badge badge-primary badge-pill">{{ number_format($detailedAnalysis['time_per_question'], 2) }} menit</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Perbandingan Kelas</h4>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Nilai Anda
                                                <span class="badge badge-primary badge-pill">{{ number_format($classComparison['student_score'], 2) }}%</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Rata-rata Kelas
                                                <span class="badge badge-{{ $classComparison['above_average'] ? 'success' : 'warning' }} badge-pill">
                                                    {{ number_format($classComparison['class_average'], 2) }}%
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Perbedaan
                                                <span class="badge badge-{{ $classComparison['difference'] >= 0 ? 'success' : 'danger' }} badge-pill">
                                                    {{ number_format($classComparison['difference'], 2) }}%
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <a href="{{ route('siswa.ujian.index') }}" class="btn btn-primary">Kembali ke Daftar Ujian</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection