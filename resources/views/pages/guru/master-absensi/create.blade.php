@extends('layouts.main')
@section('title', 'Buat Master Absensi')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Buat Master Absensi</h4>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')

                            <form action="{{ route('absensi.master.store') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="kelas_id">Kelas</label>
                                    <select name="kelas_id" id="kelas_id" class="form-control select2" required>
                                        <option value="">Pilih Kelas</option>
                                        @foreach($kelas as $item)
                                            <option value="{{ $item->id }}" {{ old('kelas_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama_kelas }}{{ $item->jurusan ? ' - ' . $item->jurusan->nama_jurusan : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="mapel_id">Mata Pelajaran</label>
                                    <select name="mapel_id" id="mapel_id" class="form-control select2" required>
                                        <option value="">Pilih Mata Pelajaran</option>
                                        @foreach($mapel as $item)
                                            <option value="{{ $item->id }}" {{ old('mapel_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama_mapel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="pertemuan">Pertemuan Ke-</label>
                                    <input type="number" name="pertemuan" id="pertemuan" class="form-control"
                                           value="{{ old('pertemuan') }}" min="1" max="100" required>
                                </div>

                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();
    });
</script>
@endpush
