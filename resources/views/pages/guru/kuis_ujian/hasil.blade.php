@extends('layouts.main')
@section('title', 'Hasil Kuis/Ujian')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Hasil {{ $kuis->judul }}</h4>
                            <a class="btn btn-primary btn-sm" href="{{ route('kuis_ujian.index') }}">Kembali</a>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>Jumlah Jawaban</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($siswa as $userId => $jawabans)
                                            @php
                                                // Check if manual scoring is required for this student
                                                $requiresManualScoring = false;
                                                foreach ($jawabans as $jawaban) {
                                                    $banksoal = $jawaban->banksoal;
                                                    if ($banksoal && in_array($banksoal->tipe_soal, ['essay', 'esai'])) {
                                                        $requiresManualScoring = true;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $jawabans->first()->user->name }}</td>
                                                <td>{{ $jawabans->count() }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('guru.kuis_ujian.lihatJawaban', [$kuis->id, $userId]) }}"
                                                            class="btn btn-info btn-sm mr-1">
                                                            <i class="fas fa-eye"></i> &nbsp; Lihat Jawaban
                                                        </a>
                                                        @if($requiresManualScoring)
                                                        <a href="{{ route('guru.kuis_ujian.manual_scoring', [$kuis->id, $userId]) }}"
                                                            class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i> &nbsp; Manual Scoring
                                                        </a>
                                                        @endif
                                                    </div>
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