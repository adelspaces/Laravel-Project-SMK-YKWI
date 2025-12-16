@extends('layouts.main')
@section('title', 'Jawaban Siswa')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Jawaban {{ $user->name }} - {{ $kuis->judul }}</h4>
                            <a class="btn btn-primary btn-sm"
                                href="{{ route('guru.kuis_ujian.hasil', $kuis->id) }}">Kembali</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Pertanyaan</th>
                                            <th>Jawaban Siswa</th>
                                            <th>Kunci Jawaban</th>
                                            <th>Nilai</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($kuis->banksoals as $index => $soal)
                                            @php
                                                $jawaban = $jawabanSiswa->get($soal->id);
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    {!! $soal->pertanyaan !!}
                                                    <br>
                                                    <small class="text-muted">Type: {{ ucfirst(str_replace('_', ' ', $soal->tipe_soal)) }}</small>
                                                </td>
                                                <td>{{ $jawaban->jawaban ?? '-' }}</td>
                                                <td>{{ $soal->kunci_jawaban ?? $soal->jawaban_benar ?? '-' }}</td>
                                                <td>{{ $jawaban->nilai ?? '-' }}</td>
                                                <td>
                                                    @if($jawaban)
                                                        @if($jawaban->status_penilaian === 'graded' && in_array($soal->tipe_soal, ['pilihan_ganda', 'benar_salah']))
                                                            <span class="badge badge-info">Penilaian Otomatis</span>
                                                        @elseif($jawaban->status_penilaian === 'graded')
                                                            <span class="badge badge-success">Dinilai</span>
                                                        @elseif(in_array($soal->tipe_soal, ['pilihan_ganda', 'benar_salah']))
                                                            <span class="badge badge-info">Penilaian Otomatis</span>
                                                        @else
                                                            <span class="badge badge-warning">Menunggu Dinilai</span>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-secondary">Tidak ada jawaban</span>
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