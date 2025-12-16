@extends('layouts.normal')
@section('title', 'Ujian')

@section('content')
    <section class="section custom-section">
        @include('partials.alert')

        <div class="section-body">
            <div class="row">
                <div class="col-10">

                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">{{ $ujian->judul }}</h3>
                            <div class="alert alert-light mb-0 py-1 px-3">
                                <strong>Sisa Waktu:</strong> <span id="timer">00:00</span>
                            </div>
                        </div>
                    </div>

                    <form id="formUjian" action="{{ route('siswa.ujian.storeJawaban', $ujian->id) }}" method="POST">
                        @csrf

                        {{-- 🔁 gunakan $banksoals (hasil acak kalau is_random = true) --}}
                        @foreach ($banksoals as $index => $soal)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>Soal {{ $index + 1 }}</strong>
                                </div>
                                <div class="card-body">
                                    <p>{!! $soal->pertanyaan !!}</p>

                                    @php $tipe = strtolower($soal->tipe_soal); @endphp

                                    @if ($tipe === 'pilihan_ganda')
                                        @foreach (['a', 'b', 'c', 'd', 'e'] as $opsi)
                                            @php $field = 'opsi_' . $opsi; @endphp
                                            @if (!empty($soal->$field))
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="jawaban[{{ $soal->id }}]" value="{{ strtoupper($opsi) }}"
                                                        id="soal{{ $soal->id }}{{ $opsi }}">
                                                    <label class="form-check-label"
                                                        for="soal{{ $soal->id }}{{ $opsi }}">
                                                        {{ strtoupper($opsi) }}. {!! $soal->$field !!}
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    @elseif (in_array($tipe, ['esai', 'essay', 'uraian']))
                                        <div class="form-group">
                                            <textarea name="jawaban[{{ $soal->id }}]" class="form-control" rows="3" placeholder="Jawaban kamu..."></textarea>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary">Kumpulkan Jawaban</button>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let waktuTersisa = {{ max(0, \Carbon\Carbon::parse($ujian->waktu_selesai)->timestamp - now()->timestamp) }};
        let autoSubmit = false;

        function updateTimer() {
            const timerElement = document.getElementById('timer');
            if (timerElement) {
                const minutes = Math.floor(waktuTersisa / 60);
                const seconds = waktuTersisa % 60;
                timerElement.textContent =
                    `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

                if (waktuTersisa <= 0) {
                    clearInterval(timerInterval);
                    autoSubmit = true;
                    alert('Waktu habis! Jawaban kamu akan dikumpulkan.');
                    document.getElementById('formUjian').submit();
                }
                waktuTersisa--;
            }
        }

        // Only start the timer if the element exists
        const timerElement = document.getElementById('timer');
        if (timerElement) {
            const timerInterval = setInterval(updateTimer, 1000);
            updateTimer();
        }

        // Cek soal kosong sebelum submit manual
        document.getElementById('formUjian').addEventListener('submit', function(e) {
            console.log('Form submission intercepted by JavaScript');
            
            if (autoSubmit) {
                console.log('Auto-submit, allowing form to proceed');
                return;
            }

            let inputs = document.querySelectorAll('input[type=radio], textarea');
            let soalTerjawab = {};
            let soalKosong = 0;

            inputs.forEach(input => {
                let name = input.name;
                if (!soalTerjawab[name]) soalTerjawab[name] = false;
                if (input.checked || (input.tagName === 'TEXTAREA' && input.value.trim() !== '')) {
                    soalTerjawab[name] = true;
                }
            });

            for (let key in soalTerjawab) {
                if (!soalTerjawab[key]) soalKosong++;
            }

            if (soalKosong > 0) {
                let konfirmasi = confirm(`Ada ${soalKosong} soal yang belum dijawab. Yakin mau mengumpulkan?`);
                if (!konfirmasi) {
                    e.preventDefault();
                    console.log('Form submission cancelled by user');
                } else {
                    console.log('User confirmed submission despite empty answers');
                }
            } else {
                console.log('All questions answered, allowing form to proceed');
            }
        });
    });
</script>
@endpush