@extends('layouts.main')
@section('title', 'Master Absensi')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Master Absensi</h4>
                            <a href="{{ route('absensi.master.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Buat Master Absensi
                            </a>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')

                            <!-- Filter Form -->
                            <form action="{{ route('absensi.index') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="kelas_id">Kelas</label>
                                            <select name="kelas_id" id="kelas_id" class="form-control select2">
                                                <option value="">Semua Kelas</option>
                                                @foreach($kelas as $item)
                                                    <option value="{{ $item->id }}" {{ request('kelas_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama_kelas }}{{ $item->jurusan ? ' - ' . $item->jurusan->nama_jurusan : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="mapel_id">Mata Pelajaran</label>
                                            <select name="mapel_id" id="mapel_id" class="form-control select2">
                                                <option value="">Semua Mata Pelajaran</option>
                                                @foreach($mapel as $item)
                                                    <option value="{{ $item->id }}" {{ request('mapel_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama_mapel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal</label>
                                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ request('tanggal') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin-top: 30px;">
                                            <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                            <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @if($masterAbsensis->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-2">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Kelas</th>
                                                <th>Mata Pelajaran</th>
                                                <th>Pertemuan</th>
                                                <th>Jumlah Siswa</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($masterAbsensis as $master)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $master->tanggal->format('d/m/Y') }}</td>
                                                    <td>{{ $master->kelas->nama_kelas ?? '-' }}{{ $master->kelas->jurusan ? ' - ' . $master->kelas->jurusan->nama_jurusan : '' }}</td>
                                                    <td>{{ $master->mapel->nama_mapel ?? '-' }}</td>
                                                    <td>{{ $master->pertemuan }}</td>
                                                    <td>{{ $master->absensiSiswa->count() }}</td>
                                                    <td>
                                                        <a href="{{ route('absensi.master.show-student-attendance', $master->id) }}"
                                                           class="btn btn-info btn-sm">
                                                            <i class="fas fa-list"></i> Lihat Absensi Siswa
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center">
                                    {{ $masterAbsensis->links() }}
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Belum ada data master absensi.
                                </div>
                            @endif
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
        // Initialize Select2
        $('.select2').select2();
    });
</script>
@endpush
