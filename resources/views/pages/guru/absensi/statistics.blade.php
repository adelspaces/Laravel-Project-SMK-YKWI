@extends('layouts.main')
@section('title', 'Statistik Absensi')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Statistik Absensi</h4>
                        <div class="card-header-action">
                            <a href="{{ route('absensi.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Form -->
                        <form action="{{ route('absensi.statistics') }}" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="kelas_id">Kelas</label>
                                        <select name="kelas_id" id="kelas_id" class="form-control" required>
                                            <option value="">-- Pilih Kelas --</option>
                                            @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}" {{ $selectedKelas==$k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kelas }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="mapel_id">Mata Pelajaran</label>
                                        <select name="mapel_id" id="mapel_id" class="form-control" required>
                                            <option value="">-- Pilih Mapel --</option>
                                            @foreach ($mapel as $m)
                                            <option value="{{ $m->id }}" {{ $selectedMapel==$m->id ? 'selected' : '' }}>
                                                {{ $m->nama_mapel }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="month">Bulan</label>
                                        <select name="month" id="month" class="form-control">
                                            @php
                                            $months = [
                                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                            ];
                                            @endphp
                                            @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{ $month==$i
                                                ? 'selected' : '' }}>
                                                {{ $months[$i] }}
                                                </option>
                                                @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="year">Tahun</label>
                                        <select name="year" id="year" class="form-control">
                                            @for ($i = date('Y') - 2; $i <= date('Y') + 1; $i++) <option
                                                value="{{ $i }}" {{ $year==$i ? 'selected' : '' }}>
                                                {{ $i }}
                                                </option>
                                                @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-search"></i> Lihat Statistik
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if (!empty($statistics))
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-info-circle"></i> Statistik Absensi</h5>
                                    <p>Periode:
                                        @php
                                        $months = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                        ];
                                        @endphp
                                        {{ $months[$month] }} {{ $year }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            @php
                            $totalStudents = count($statistics);
                            $avgAttendanceRate = $totalStudents > 0 ? collect($statistics)->avg('attendance_rate') : 0;
                            $excellentAttendance = collect($statistics)->where('attendance_rate', '>=', 90)->count();
                            $poorAttendance = collect($statistics)->where('attendance_rate', '<', 75)->count();
                                @endphp

                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon bg-primary">
                                            <i class="far fa-user"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Total Siswa</h4>
                                            </div>
                                            <div class="card-body">
                                                {{ $totalStudents }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon bg-success">
                                            <i class="fas fa-percentage"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Rata-rata Kehadiran</h4>
                                            </div>
                                            <div class="card-body">
                                                {{ number_format($avgAttendanceRate, 1) }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon bg-warning">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Kehadiran Excellent (≥90%)</h4>
                                            </div>
                                            <div class="card-body">
                                                {{ $excellentAttendance }} siswa
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon bg-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Perlu Perhatian (<75%)</h4>
                                            </div>
                                            <div class="card-body">
                                                {{ $poorAttendance }} siswa
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>

                        <!-- Detailed Statistics Table -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Detail Statistik Siswa</h4>
                                <div class="card-header-action">
                                    <form action="{{ route('absensi.export') }}" method="GET" class="d-inline">
                                        <input type="hidden" name="kelas_id" value="{{ $selectedKelas }}">
                                        <input type="hidden" name="mapel_id" value="{{ $selectedMapel }}">
                                        <input type="hidden" name="start_date"
                                            value="{{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}-01">
                                        <input type="hidden" name="end_date"
                                            value="{{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}-{{ date('t', mktime(0, 0, 0, $month, 1, $year)) }}">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> Export Excel
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-2">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nama Siswa</th>
                                                <th>Total Hari</th>
                                                <th>Hari Hadir</th>
                                                <th>Tingkat Kehadiran</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($statistics as $index => $stat)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $stat['siswa']->nama }}</td>
                                                <td>{{ $stat['total_days'] }}</td>
                                                <td>{{ $stat['present_days'] }}</td>
                                                <td>
                                                    <div class="progress mb-1" style="height: 20px;">
                                                        @php
                                                        $rate = $stat['attendance_rate'];
                                                        $color = $rate >= 90 ? 'success' : ($rate >= 75 ? 'warning' :
                                                        'danger');
                                                        @endphp
                                                        <div class="progress-bar bg-{{ $color }}"
                                                            style="width: {{ $rate }}%">
                                                            {{ number_format($rate, 1) }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($stat['attendance_rate'] >= 90)
                                                    <span class="badge badge-success">Excellent</span>
                                                    @elseif ($stat['attendance_rate'] >= 75)
                                                    <span class="badge badge-warning">Good</span>
                                                    @else
                                                    <span class="badge badge-danger">Perlu Perhatian</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i>
                            Silakan pilih kelas dan mata pelajaran untuk melihat statistik absensi.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
