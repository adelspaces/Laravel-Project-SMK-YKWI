@extends('layouts.main')
@section('title', 'Kuis & Ujian')

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @include('partials.alert')
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Tambah Kuis dan Ujian</h4>

                        </div>
                        <div class="container">
                            <form action="{{ route('kuis_ujian.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex justify-content-between mt-3">
                                    <a></a>
                                    {{-- <a href="{{ route('kuis_ujian.index') }}" class="btn btn-primary">
                                        Kembali
                                    </a> --}}
                                    <button type="submit" class="btn btn-success">
                                        Simpan
                                    </button>
                                </div>
                                <div></div>
                                <div class="mb-3">
                                    <label for="judul" class="form-label">Judul</label>
                                    <input type="text" name="judul" id="judul" class="form-control"
                                        value="{{ old('judul') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="tipe" class="form-label">Tipe</label>
                                    <select name="tipe" id="tipe" class="form-control" required>
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="kuis">Kuis</option>
                                        <option value="ujian">Ujian</option>
                                    </select>
                                </div>

                                <div id="maksimalPercobaanField" class="mb-3" style="display: none;">
                                    <label for="max_attempt" class="form-label">Maksimal Percobaan</label>
                                    <input type="number" name="max_attempt" id="max_attempt" class="form-control"
                                        min="1">
                                </div>

                                <div class="mb-3">
                                    <label for="waktu_mulai" class="form-label">Kapan dimulai</label>
                                    <input type="datetime-local" name="waktu_mulai" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="waktu_selesai" class="form-label">Tenggat waktu</label>
                                    <input type="datetime-local" name="waktu_selesai" id="waktu_selesai"
                                        class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="mapel_id">Mata Pelajaran</label>
                                    <select name="mapel_id" id="mapel_id" class="form-control" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach ($mapels as $mapel)
                                            <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="is_random">Acak Soal?</label>
                                    <select name="is_random" id="is_random" class="form-control">
                                        <option value="0">Tidak</option>
                                        <option value="1">Ya</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="banksoals">Pilih Soal dari Banksoal</label>
                                    <div class="d-flex align-items-center">
                                        <select name="banksoals[]" id="banksoals" class="form-control select2" multiple
                                            required data-placeholder="-- Pilih Soal dari Banksoal --"
                                            style="width: calc(100% - 110px);">
                                            @foreach ($banksoals as $soal)
                                                <option value="{{ $soal->id }}" data-mapel="{{ $soal->mapel_id }}">
                                                    [{{ $soal->mapel->nama_mapel ?? '-' }}]
                                                    {{ Str::limit($soal->pertanyaan, 50) }} -
                                                    {{ ucfirst($soal->tipe_soal) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" id="select-all-banksoals"
                                            class="btn btn-outline-primary ml-2"
                                            style="height: 38px; width: auto; white-space: nowrap;">Pilih Semua</button>
                                    </div>
                                    <small class="form-text text-muted">Pilih mata pelajaran terlebih dahulu untuk memfilter
                                        soal yang tersedia</small>
                                </div>
                                {{-- <button type="submit" class="btn btn-success mt-3">Simpan</button> --}}
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

            // Make sure the select2 container and button are aligned
            $('.select2-container--default').css('height', '38px');

            // Add Select All functionality
            $('#select-all-banksoals').on('click', function() {
                var selectElement = $('#banksoals');
                var visibleOptions = selectElement.find('option:not([style*="display: none"])');

                // Get all visible option values
                var values = visibleOptions.map(function() {
                    return this.value;
                }).get();

                // Set the selected values
                selectElement.val(values).trigger('change');
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

                // Clear current selection
                banksoalSelect.val(null).trigger('change');

                if (selectedMapel) {
                    // Show only options that match the selected mapel
                    allOptions.each(function() {
                        const option = $(this);
                        const optionMapel = option.data('mapel');

                        if (optionMapel == selectedMapel) {
                            option.show();
                        } else {
                            option.hide();
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
