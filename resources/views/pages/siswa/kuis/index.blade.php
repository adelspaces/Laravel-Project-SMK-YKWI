@extends('layouts.main')
@section('title', 'Kuis')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Kuis</h4>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul Kuis</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Batas waktu</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kuis as $kuisItem)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $kuisItem->judul }}</td>
                                        <td>{{ $kuisItem->mapel->nama_mapel }}</td>
                                        <td>{{ \Carbon\Carbon::parse($kuisItem->waktu_selesai)->format('d M Y H:i') }}
                                        </td>
                                        <td>
                                            @php
                                            $now = now();
                                            $isStarted = $now->gte($kuisItem->waktu_mulai);
                                            $isEnded = $now->gt($kuisItem->waktu_selesai);

                                            // hitung jumlah percobaan yang sudah dilakukan user
                                            $jumlahPercobaan = DB::table('hasil_ujians')
                                            ->where('user_id', Auth::id())
                                            ->where('kuis_ujian_id', $kuisItem->id)
                                            ->count();

                                            $percobaanHabis = $jumlahPercobaan >= $kuisItem->max_attempt;
                                            @endphp

                                            @if (!$isStarted)
                                            <button class="btn btn-secondary" disabled>Belum Dimulai</button>
                                            <small class="text-muted d-block">
                                                Mulai:
                                                {{ \Carbon\Carbon::parse($kuisItem->waktu_mulai)->format('d M Y H:i') }}
                                            </small>
                                            @elseif ($isEnded)
                                            <button class="btn btn-danger" disabled>Sudah Berakhir</button>
                                            <small class="text-muted d-block">
                                                Anda tidak mengerjakan kuis
                                                {{-- Tenggat:
                                                {{ \Carbon\Carbon::parse($kuisItem->waktu_selesai)->format('d M Y H:i')
                                                }} --}}
                                            </small>
                                            @elseif ($percobaanHabis)
                                            <button class="btn btn-secondary" disabled>Percobaan Habis</button>
                                            <small class="text-muted d-block">
                                                Percobaan: {{ $jumlahPercobaan }} / {{ $kuisItem->max_attempt }}
                                            </small>
                                            @else
                                            <a href="{{ route('siswa.kuis.show', $kuisItem->id) }}"
                                                class="btn btn-primary">
                                                Kerjakan
                                            </a>
                                            <small class="text-muted d-block">
                                                Percobaan: {{ $jumlahPercobaan }} /
                                                {{ $kuisItem->max_attempt }}
                                            </small>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada kuis.</td>
                                    </tr>
                                    @endforelse
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
