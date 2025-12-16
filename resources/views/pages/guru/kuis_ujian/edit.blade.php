@extends('layouts.main')
@section('title', 'Edit Kuis & Ujian')

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Edit Kuis dan Ujian</h4>
                            <a class="btn btn-primary btn-sm" href="{{ route('kuis_ujian.index') }}">Kembali</a>
                        </div>
                        <div class="container">
                            <form action="{{ route('kuis_ujian.update', $kuisUjian->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="judul" class="form-label">Judul</label>
                                    <input type="text" name="judul" id="judul" class="form-control"
                                        value="{{ old('judul', $kuisUjian->judul) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="tipe" class="form-label">Tipe</label>
                                    <select name="tipe" id="tipe" class="form-control" required>
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="kuis" {{ $kuisUjian->tipe === 'kuis' ? 'selected' : '' }}>Kuis
                                        </option>
                                        <option value="ujian" {{ $kuisUjian->tipe === 'ujian' ? 'selected' : '' }}>Ujian
                                        </option>
                                    </select>
                                </div>

                                <div id="maksimalPercobaanField" class="mb-3"
                                    style="{{ $kuisUjian->tipe === 'kuis' ? '' : 'display: none;' }}">
                                    <label for="max_attempt" class="form-label">Maksimal Percobaan</label>
                                    <input type="number" name="max_attempt" id="max_attempt" class="form-control"
                                        min="1" value="{{ old('max_attempt', $kuisUjian->max_attempt) }}">
                                </div>

                                <div class="mb-3">
                                    <label for="waktu_mulai" class="form-label">Kapan dimulai</label>
                                    <input type="datetime-local" name="waktu_mulai" class="form-control"
                                        value="{{ old('waktu_mulai', \Carbon\Carbon::parse($kuisUjian->waktu_mulai)->format('Y-m-d\TH:i')) }}"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="waktu_selesai" class="form-label">Tenggat waktu</label>
                                    <input type="datetime-local" name="waktu_selesai" id="waktu_selesai"
                                        class="form-control"
                                        value="{{ old('waktu_selesai', \Carbon\Carbon::parse($kuisUjian->waktu_selesai)->format('Y-m-d\TH:i')) }}"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="mapel_id">Mata Pelajaran</label>
                                    <select name="mapel_id" id="mapel_id" class="form-control" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach ($mapels as $mapel)
                                            <option value="{{ $mapel->id }}"
                                                {{ $kuisUjian->mapel_id == $mapel->id ? 'selected' : '' }}>
                                                {{ $mapel->nama_mapel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="banksoals">Pilih Soal dari Banksoal</label>
                                    <select name="banksoals[]" id="banksoals" class="form-control select2" multiple required data-placeholder="-- Pilih Soal dari Banksoal --">
                                        @foreach ($banksoals as $soal)
                                            <option value="{{ $soal->id }}" data-mapel="{{ $soal->mapel_id }}"
                                                {{ in_array($soal->id, $kuisUjian->banksoals->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                [{{ $soal->mapel->nama_mapel ?? '-' }}]
                                                {{ Str::limit($soal->pertanyaan, 50) }} - {{ ucfirst($soal->tipe_soal) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Pilih mata pelajaran terlebih dahulu untuk memfilter soal yang tersedia</small>
                                </div>

                                <div class="form-group">
                                    <label for="is_random">Acak Soal?</label>
                                    <select name="is_random" id="is_random" class="form-control">
                                        <option value="0" {{ $kuisUjian->is_random == 0 ? 'selected' : '' }}>Tidak
                                        </option>
                                        <option value="1" {{ $kuisUjian->is_random == 1 ? 'selected' : '' }}>Ya
                                        </option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success mt-3">Perbarui</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#banksoals').select2({
                placeholder: "-- Pilih Soal dari Banksoal --",
                allowClear: true,
                width: '100%'
            });

            const tipeSelect = document.getElementById('tipe');
            const maxAttemptGroup = document.getElementById('maksimalPercobaanField');
            const mapelSelect = document.getElementById('mapel_id');
            const banksoalSelect = $('#banksoals');

            function toggleFields() {
                if (tipeSelect.value === 'kuis') {
                    maxAttemptGroup.style.display = 'block';
                } else {
                    maxAttemptGroup.style.display = 'none';
                }
            }

            // Filter banksoal based on selected mapel
            function filterBanksoal() {
                const selectedMapel = mapelSelect.value;
                const allOptions = banksoalSelect.find('option');
                const currentSelection = banksoalSelect.val(); // Preserve current selection
                
                if (selectedMapel) {
                    // Show only options that match the selected mapel
                    allOptions.each(function() {
                        const option = $(this);
                        const optionMapel = option.data('mapel');
                        
                        if (optionMapel == selectedMapel) {
                            option.show();
                        } else {
                            option.hide();
                            // Remove from selection if hidden
                            if (currentSelection && currentSelection.includes(option.val())) {
                                const newSelection = currentSelection.filter(val => val !== option.val());
                                banksoalSelect.val(newSelection).trigger('change');
                            }
                        }
                    });
                } else {
                    // Show all options if no mapel selected
                    allOptions.show();
                }
                
                // Refresh Select2 to reflect changes
                banksoalSelect.trigger('change');
            }

            // Event listeners
            toggleFields();
            tipeSelect.addEventListener('change', toggleFields);
            mapelSelect.addEventListener('change', filterBanksoal);
            
            // Initial filter on page load
            filterBanksoal();
        });
    </script>
@endsection
