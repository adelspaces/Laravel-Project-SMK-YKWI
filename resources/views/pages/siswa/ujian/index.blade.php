@extends('layouts.main')
@section('title', 'Ujian')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Ujian</h4>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul Ujian</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Batas Waktu</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($ujian as $ujianItem)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $ujianItem->judul }}</td>
                                        <td>{{ $ujianItem->mapel->nama_mapel }}</td>
                                        <td>{{ \Carbon\Carbon::parse($ujianItem->waktu_selesai)->format('d M Y H:i') }}
                                        <td>
                                            @php
                                            $now = now();
                                            $isStarted = $now->gte($ujianItem->waktu_mulai);
                                            $isEnded = $now->gt($ujianItem->waktu_selesai);

                                            $sudahDikerjakan = DB::table('jawaban_siswas')
                                            ->where('user_id', Auth::id())
                                            ->where('kuis_ujian_id', $ujianItem->id)
                                            ->exists();
                                            @endphp

                                            @if (!$isStarted)
                                            <button class="btn btn-secondary" disabled>Belum Dimulai</button>
                                            <small class="text-muted d-block">
                                                Mulai:
                                                {{ \Carbon\Carbon::parse($ujianItem->waktu_mulai)->format('d M Y H:i')
                                                }}
                                            </small>
                                            @elseif ($isEnded)
                                            <button class="btn btn-danger" disabled>Sudah Berakhir</button>
                                            <small class="text-muted d-block">
                                                Anda tidak megerjakan ujian
                                                {{-- Tenggat:
                                                {{ \Carbon\Carbon::parse($ujianItem->waktu_selesai)->format('d M Y H:i')
                                                }} --}}
                                            </small>
                                            @elseif ($sudahDikerjakan)
                                            <button class="btn btn-secondary" disabled>Terkumpul</button>
                                            @else
                                            <a href="{{ route('siswa.ujian.show', $ujianItem->id) }}"
                                                class="btn btn-primary">
                                                Kerjakan
                                            </a>
                                            @endif

                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada ujian.</td>
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
