@extends('layouts.main')

@section('title', 'Riwayat Absensi')

@section('content')
    <section class="section custom-section">
        <div class="section-header">
            <h1>Riwayat Absensi</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                            <th>Mapel</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($absensis as $absen)
                                            <tr>
                                                <td>{{ $absen->tanggal }}</td>
                                                <td>{{ $absen->siswa->nama }}</td>
                                                <td>{{ $absen->kelas->nama_kelas }}</td>
                                                <td>{{ $absen->mapel->nama_mapel }}</td>
                                                <td>
                                                    @if($absen->status == 'hadir')
                                                        <span class="badge badge-success">Hadir</span>
                                                    @elseif($absen->status == 'izin')
                                                        <span class="badge badge-warning">Izin</span>
                                                    @elseif($absen->status == 'sakit')
                                                        <span class="badge badge-info">Sakit</span>
                                                    @elseif($absen->status == 'alfa')
                                                        <span class="badge badge-danger">Alfa</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
