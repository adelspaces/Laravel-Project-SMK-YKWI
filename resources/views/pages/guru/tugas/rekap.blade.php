@extends('layouts.main')
@section('title', 'Rekap Tugas')

@section('content')
<h4>Rekap Nilai: {{ $tugas->judul }}</h4>
<div class="row g-3 mb-3">
    <div class="col">Total Submit: <strong>{{ $stat['total_submit'] }}</strong></div>
    <div class="col">Sudah Dinilai: <strong>{{ $stat['sudah_dinilai'] }}</strong></div>
    <div class="col">Belum Dinilai: <strong>{{ $stat['belum_dinilai'] }}</strong></div>
    <div class="col">Rata-rata: <strong>{{ $stat['rata2'] }}</strong></div>
    <div class="col">Min: <strong>{{ $stat['min'] ?? '-' }}</strong></div>
    <div class="col">Max: <strong>{{ $stat['max'] ?? '-' }}</strong></div>
    <div class="col text-end">
        <a href="{{ route('tugas.rekap.export', $tugas) }}" class="btn btn-success btnsm">Export Excel</a>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-sm align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>NIS/Nama</th>
                <th>Jawaban</th>
                <th>File</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jawabans as $i => $j)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $j->siswa->nis ?? '-' }} / {{ $j->siswa->nama ?? '-' }}</td>
                <td style="max-width:380px">{{ Str::limit($j->jawaban, 120) }}</td>
                <td>
                    @if($j->file)
                    <a href="{{ Storage::url($j->file) }}" target="_blank">File</a>
                    @else
                    -
                    @endif
                </td>
                <td>{{ $j->nilai ?? 'Belum' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada jawaban</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
