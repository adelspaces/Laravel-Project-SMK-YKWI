@extends('layouts.main')
@section('title', 'Manual Scoring')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Manual Scoring - {{ $kuis->judul }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('kuis_ujian.index') }}">Kuis/Ujian</a></div>
            <div class="breadcrumb-item"><a href="{{ route('guru.kuis_ujian.hasil', $kuis->id) }}">Hasil</a></div>
            <div class="breadcrumb-item">Manual Scoring</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Student: {{ $user->name }}</h4>
                    </div>
                    <div class="card-body">
                        <form id="manual-scoring-form">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Question</th>
                                            <th>Student Answer</th>
                                            <th>Score (0-100)</th>
                                            <th>Feedback</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($kuis->banksoals as $banksoal)
                                            @php
                                                $jawaban = $jawabanSiswa->get($banksoal->id);
                                                $isGraded = $jawaban && $jawaban->status_penilaian === 'graded';
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{!! $banksoal->pertanyaan !!}</strong>
                                                    <br>
                                                    <small class="text-muted">Type: {{ ucfirst(str_replace('_', ' ', $banksoal->tipe_soal)) }}</small>
                                                    <br>
                                                    @if($banksoal->tipe_soal == 'pilihan_ganda')
                                                        <small class="text-info">Correct Answer: {{ $banksoal->kunci_jawaban }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($jawaban)
                                                        {{ $jawaban->jawaban }}
                                                        @if($isGraded)
                                                            <br><small class="text-success">Already graded: {{ $jawaban->nilai }}</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">No answer provided</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           name="scores[{{ $banksoal->id }}][nilai]" 
                                                           class="form-control score-input" 
                                                           min="0" 
                                                           max="100" 
                                                           step="0.1"
                                                           value="{{ $jawaban->nilai ?? '' }}"
                                                           {{ $isGraded ? 'readonly' : '' }}
                                                           {{ in_array($banksoal->tipe_soal, ['essay', 'esai']) ? 'required' : '' }}>
                                                    <input type="hidden" name="scores[{{ $banksoal->id }}][banksoal_id]" value="{{ $banksoal->id }}">
                                                </td>
                                                <td>
                                                    <textarea name="scores[{{ $banksoal->id }}][feedback]" 
                                                              class="form-control" 
                                                              rows="2"
                                                              {{ $isGraded ? 'readonly' : '' }}
                                                              placeholder="Provide feedback to student...">{{ $jawaban->feedback ?? '' }}</textarea>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No essay questions found for this exam.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" id="submit-scores">Submit Scores</button>
                                <a href="{{ route('guru.kuis_ujian.hasil', $kuis->id) }}" class="btn btn-secondary">Back to Results</a>
                            </div>
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
        $('#manual-scoring-form').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const submitBtn = $('#submit-scores');
            const originalBtnText = submitBtn.text();
            
            submitBtn.prop('disabled', true).text('Submitting...');
            
            $.ajax({
                url: "{{ route('guru.kuis_ujian.store_manual_scores', [$kuis->id, $user->id]) }}",
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        }).then(() => {
                            window.location.href = "{{ route('guru.kuis_ujian.hasil', $kuis->id) }}";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to submit scores. Please try again.'
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text(originalBtnText);
                }
            });
        });
    });
</script>
@endpush