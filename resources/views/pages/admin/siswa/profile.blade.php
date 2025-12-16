@extends('layouts.main')
@section('title', 'Profile Siswa')

@section('content')
    <div class="section">
        <div class="section-body">
            <div class="row d-flex justify-content-center">
                <div class="col-12 col-sm-12 col-lg-5">
                    <div class="card profile-widget">
                        <div class="profile-widget-header">
                            @if ($siswa->foto)
                                <img src="{{ asset('img/siswa/' . $siswa->foto) }}" alt="Foto Siswa" width="120">
                            @else
                                <img src="{{ asset('img/siswa/default.png') }}" alt="Foto Default" width="120">
                            @endif
                            <div class="profile-widget-items">
                                <div class="profile-widget-item">
                                    <div class="profile-widget-item-label">NIP</div>
                                    <div class="profile-widget-item-value">{{ $siswa->nis }}</div>
                                </div>
                                <div class="profile-widget-item">
                                    <div class="profile-widget-item-label">Telp</div>
                                    <div class="profile-widget-item-value">{{ $siswa->telp }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-widget-description pb-0">
                            <div class="profile-widget-name">{{ $siswa->nama }}
                                <div class="text-muted d-inline font-weight-normal">
                                    <div class="slash"></div> siswa {{ $siswa->kelas->nama_kelas }}
                                </div>
                            </div>
                            <label for="alamat">Alamat</label>
                            <p>{{ $siswa->alamat }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
