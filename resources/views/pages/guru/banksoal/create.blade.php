@extends('layouts.main')
@section('title', 'banksoal')

@section('content')
<section class="section">
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Tambah Soal</h4>
                            <a class="btn btn-primary btn-sm" href="{{ route('banksoal.index') }}">Kembali</a>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('banksoal.store') }}" enctype="multipart/form-data">
                                @csrf

                                {{-- Mata Pelajaran --}}
                                <div class="form-group mt-3">
                                    <label for="mapel_id">Mata Pelajaran</label>
                                    <select name="mapel_id" class="form-control" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach ($mapels as $mapel)
                                        <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Tipe Soal --}}
                                <div class="form-group mt-3">
                                    <label for="tipe_soal">Tipe Soal</label>
                                    <select name="tipe_soal" id="tipe_soal" class="form-control" required
                                        onchange="toggleForm()">
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="pilihan_ganda">Pilihan Ganda</option>
                                        <option value="esai">Esai</option>
                                    </select>
                                </div>

                                {{-- Pertanyaan --}}
                                <div class="form-group mt-3">
                                    <label for="pertanyaan">Pertanyaan</label>
                                    <textarea name="pertanyaan" class="form-control" rows="3" required></textarea>
                                </div>

                                {{-- Pilihan Ganda --}}
                                <div id="pg-fields" style="display: none;">
                                    <div class="form-group mt-3">
                                        <label for="opsi_a">Opsi A</label>
                                        <input type="text" class="form-control" id="opsi_a" name="opsi_a">
                                    </div>
                                    <div class="form-group">
                                        <label for="opsi_b">Opsi B</label>
                                        <input type="text" class="form-control" id="opsi_b" name="opsi_b">
                                    </div>
                                    <div class="form-group">
                                        <label for="opsi_c">Opsi C</label>
                                        <input type="text" class="form-control" id="opsi_c" name="opsi_c">
                                    </div>
                                    <div class="form-group">
                                        <label for="opsi_d">Opsi D</label>
                                        <input type="text" class="form-control" id="opsi_d" name="opsi_d">
                                    </div>
                                    <div class="form-group">
                                        <label for="jawaban_benar">Jawaban Benar</label>
                                        <select class="form-control" id="jawaban_benar" name="jawaban_benar">
                                            <option value="">-- Pilih Jawaban --</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Kunci Jawaban Esai --}}
                                <div id="esai-fields" style="display: none;">
                                    <div class="form-group mt-3">
                                        <label for="kunci_jawaban">Kunci Jawaban Esai</label>
                                        <textarea class="form-control" id="kunci_jawaban" name="kunci_jawaban"
                                            rows="4"></textarea>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success mt-3">Simpan</button>
                            </form>
                        </div>

                        {{-- Script Toggle Form --}}
                        <script>
                            function toggleForm() {
                                    const tipe = document.getElementById('tipe_soal').value;
                                    const pgFields = document.getElementById('pg-fields');
                                    const esaiFields = document.getElementById('esai-fields');

                                    if (tipe === 'pilihan_ganda') {
                                        pgFields.style.display = 'block';
                                        esaiFields.style.display = 'none';
                                    } else if (tipe === 'esai') {
                                        pgFields.style.display = 'none';
                                        esaiFields.style.display = 'block';
                                    } else {
                                        pgFields.style.display = 'none';
                                        esaiFields.style.display = 'none';
                                    }
                                }

                                document.addEventListener('DOMContentLoaded', toggleForm);
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>
@endsection
