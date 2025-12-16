@extends('layouts.main')
@section('title', 'Statistik Absensi')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Statistik Absensi Saya</h4>
                        <div class="card-header-action">
                            <a href="{{ route('absensi.siswa.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Form -->
                        <form action="{{ route('absensi.siswa.statistics') }}" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
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
                                <div class="col-md-3">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-search"></i> Lihat Statistik
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

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
                            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-primary">
                                        <i class="far fa-calendar"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Total Hari</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $totalDays }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Hari Hadir</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $presentDays }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-warning">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Izin</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $izinDays }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-info">
                                        <i class="fas fa-file-medical"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Sakit</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $sakitDays }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Rate Card -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Tingkat Kehadiran</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h2>{{ number_format($attendanceRate, 1) }}%</h2>
                                                <p>Dari {{ $totalDays }} hari pembelajaran</p>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="progress" style="height: 30px;">
                                                    @php
                                                    $color = $attendanceRate >= 90 ? 'success' : ($attendanceRate >= 75 ? 'warning' : 'danger');
                                                    @endphp
                                                    <div class="progress-bar bg-{{ $color }}"
                                                         style="width: {{ $attendanceRate }}%; height: 30px; line-height: 30px;">
                                                        {{ number_format($attendanceRate, 1) }}%
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    @if ($attendanceRate >= 90)
                                                        <span class="badge badge-success">Excellent</span>
                                                    @elseif ($attendanceRate >= 75)
                                                        <span class="badge badge-warning">Good</span>
                                                    @else
                                                        <span class="badge badge-danger">Perlu Perhatian</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Attendance Records -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Riwayat Absensi</h4>
                            </div>
                            <div class="card-body">
                                @if ($studentAttendances->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-absensi-siswa">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Tanggal</th>
                                                <th>Mata Pelajaran</th>
                                                <th>Guru</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($studentAttendances as $index => $attendance)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $attendance->masterAbsensi->tanggal->format('d/m/Y') }}</td>
                                                <td>{{ $attendance->masterAbsensi->mapel->nama_mapel ?? '-' }}</td>
                                                <td>{{ $attendance->masterAbsensi->guru->nama ?? '-' }}</td>
                                                <td>
                                                    @if($attendance->status == 'hadir')
                                                        <span class="badge badge-success">Hadir</span>
                                                    @elseif($attendance->status == 'izin')
                                                        <span class="badge badge-warning">Izin</span>
                                                    @elseif($attendance->status == 'sakit')
                                                        <span class="badge badge-info">Sakit</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle"></i>
                                    Tidak ada data absensi untuk periode ini.
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Initialize DataTable for student attendance
        if ($.fn.DataTable.isDataTable('#table-absensi-siswa')) {
            $('#table-absensi-siswa').DataTable().destroy();
        }

        $('#table-absensi-siswa').DataTable({
            columnDefs: [{ sortable: false, targets: [4] }], // Disable sorting on status column
            pageLength: 10,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang cocok",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya",
                },
            },
        });
    });
</script>
@endpush
