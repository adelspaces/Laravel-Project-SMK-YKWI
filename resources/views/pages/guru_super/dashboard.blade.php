@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <!-- Panel Ringkasan -->
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Guru Aktif</h5>
                            <p class="card-text">{{ $jumlahGuru }} Guru</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Siswa Terdaftar</h5>
                            <p class="card-text">{{ $jumlahSiswa }} Siswa</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Materi Diunggah</h5>
                            <p class="card-text">{{ $jumlahMateri }} Materi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Tugas Mingguan</h5>
                            <p class="card-text">{{ $jumlahTugas }} Tugas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
