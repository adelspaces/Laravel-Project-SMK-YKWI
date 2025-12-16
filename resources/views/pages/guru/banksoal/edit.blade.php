@extends('layouts.main')
@section('title', 'Absensi')

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<section class="section">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Edit Bank Soal</h4>
        </div>
        <div class="card-body">
            <div class="container">
                <form method="POST" action="{{ route('banksoal.update', $soal->id) }}">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="mapel_id" value="{{ $soal->mapel_id }}">

                    <div class="mb-3">
                        <label for="pertanyaan" class="form-label">Pertanyaan</label>
                        <textarea class="form-control" name="pertanyaan" id="pertanyaan"
                            required>{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tipe_soal" class="form-label">Tipe Soal</label>
                        <select class="form-select" name="tipe_soal" id="tipe_soal" required>
                            <option value="pilihan_ganda" {{ $soal->tipe_soal === 'pilihan_ganda' ? 'selected' : ''
                                }}>
                                Pilihan
                                Ganda</option>
                            <option value="esai" {{ $soal->tipe_soal === 'esai' ? 'selected' : '' }}>Esai</option>
                        </select>
                    </div>

                    <div id="opsi-pg" style="{{ $soal->tipe_soal === 'pilihan_ganda' ? '' : 'display: none;' }}">
                        <div class="mb-3">
                            <label for="opsi_a" class="form-label">Opsi A</label>
                            <input type="text" class="form-control" name="opsi_a" value="{{ $soal->opsi_a }}">
                        </div>
                        <div class="mb-3">
                            <label for="opsi_b" class="form-label">Opsi B</label>
                            <input type="text" class="form-control" name="opsi_b" value="{{ $soal->opsi_b }}">
                        </div>
                        <div class="mb-3">
                            <label for="opsi_c" class="form-label">Opsi C</label>
                            <input type="text" class="form-control" name="opsi_c" value="{{ $soal->opsi_c }}">
                        </div>
                        <div class="mb-3">
                            <label for="opsi_d" class="form-label">Opsi D</label>
                            <input type="text" class="form-control" name="opsi_d" value="{{ $soal->opsi_d }}">
                        </div>
                        <div class="mb-3">
                            <label for="jawaban_benar" class="form-label">Jawaban Benar</label>
                            <select name="jawaban_benar" class="form-select">
                                <option value="A" {{ $soal->jawaban_benar === 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ $soal->jawaban_benar === 'B' ? 'selected' : '' }}>B</option>
                                <option value="C" {{ $soal->jawaban_benar === 'C' ? 'selected' : '' }}>C</option>
                                <option value="D" {{ $soal->jawaban_benar === 'D' ? 'selected' : '' }}>D</option>
                            </select>
                        </div>
                    </div>

                    <div id="kunci-esai" style="{{ $soal->tipe_soal === 'esai' ? '' : 'display: none;' }}">
                        <div class="mb-3">
                            <label for="kunci_jawaban" class="form-label">Kunci Jawaban (Esai)</label>
                            <textarea class="form-control" name="kunci_jawaban"
                                id="kunci_jawaban">{{ $soal->kunci_jawaban }}</textarea>
                        </div>
                    </div>

                    {{-- <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="{{ route('banksoal.index') }}" class="btn btn-secondary">Batal</a> --}}

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('banksoal.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>
<script>
    document.getElementById('tipe_soal').addEventListener('change', function() {
                const tipe = this.value;
                document.getElementById('opsi-pg').style.display = tipe === 'pilihan_ganda' ? 'block' : 'none';
                document.getElementById('kunci-esai').style.display = tipe === 'esai' ? 'block' : 'none';
            });
</script>
@endsection
