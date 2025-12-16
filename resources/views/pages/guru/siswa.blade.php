@extends('layouts.main')
@section('title', 'Siswa Saya')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Daftar Siswa Kelas Anda</h1>
        </div>

        <div class="section-body">
            @if ($siswas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-2">
                        <thead>
                            <tr>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Alamat</th>
                                <th>No Telepon</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($siswas as $siswa)
                                <tr>
                                    <td>{{ $siswa->nis }}</td>
                                    <td>{{ $siswa->nama }}</td>
                                    <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                    <td>{{ $siswa->alamat }}</td>
                                    <td>{{ $siswa->telp }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Tidak ada siswa yang terdaftar pada kelas Anda.
                </div>
            @endif
        </div>
    </section>
@endsection
