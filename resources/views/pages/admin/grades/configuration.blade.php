@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Konfigurasi Nilai</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.grades.updateConfiguration') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="attendance_weight">Bobot Absensi (%)</label>
                                    <input type="number" class="form-control" id="attendance_weight"
                                        name="attendance_weight"
                                        value="{{ old('attendance_weight', $config->attendance_weight) }}" min="0"
                                        max="100" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="assessment_weight">Bobot Penilaian (%)</label>
                                    <input type="number" class="form-control" id="assessment_weight"
                                        name="assessment_weight"
                                        value="{{ old('assessment_weight', $config->assessment_weight) }}" min="0"
                                        max="100" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tugas_weight">Bobot Tugas (%)</label>
                                    <input type="number" class="form-control" id="tugas_weight" name="tugas_weight"
                                        value="{{ old('tugas_weight', $config->tugas_weight) }}" min="0" max="100"
                                        step="0.01" required>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5>Ambang Batas Nilai</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold_a">A (Nilai Minimum)</label>
                                    <input type="number" class="form-control" id="threshold_a" name="threshold_a"
                                        value="{{ old('threshold_a', $config->grade_thresholds['A'] ?? 80) }}" min="0"
                                        max="100" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold_a_minus">A- (Nilai Minimum)</label>
                                    <input type="number" class="form-control" id="threshold_a_minus"
                                        name="threshold_a_minus"
                                        value="{{ old('threshold_a_minus', $config->grade_thresholds['A-'] ?? 75) }}"
                                        min="0" max="100" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold_b_plus">B+ (Nilai Minimum)</label>
                                    <input type="number" class="form-control" id="threshold_b_plus"
                                        name="threshold_b_plus"
                                        value="{{ old('threshold_b_plus', $config->grade_thresholds['B+'] ?? 70) }}"
                                        min="0" max="100" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold_b">B (Nilai Minimum)</label>
                                    <input type="number" class="form-control" id="threshold_b" name="threshold_b"
                                        value="{{ old('threshold_b', $config->grade_thresholds['B'] ?? 60) }}" min="0"
                                        max="100" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold_b_minus">B- (Nilai Minimum)</label>
                                    <input type="number" class="form-control" id="threshold_b_minus"
                                        name="threshold_b_minus"
                                        value="{{ old('threshold_b_minus', $config->grade_thresholds['B-'] ?? 55) }}"
                                        min="0" max="100" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold_c_plus">C+ (Nilai Minimum)</label>
                                    <input type="number" class="form-control" id="threshold_c_plus"
                                        name="threshold_c_plus"
                                        value="{{ old('threshold_c_plus', $config->grade_thresholds['C+'] ?? 50) }}"
                                        min="0" max="100" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold_c">C (Nilai Minimum)</label>
                                    <input type="number" class="form-control" id="threshold_c" name="threshold_c"
                                        value="{{ old('threshold_c', $config->grade_thresholds['C'] ?? 40) }}" min="0"
                                        max="100" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold_c_minus">C- (Nilai Minimum)</label>
                                    <input type="number" class="form-control" id="threshold_c_minus"
                                        name="threshold_c_minus"
                                        value="{{ old('threshold_c_minus', $config->grade_thresholds['C-'] ?? 35) }}"
                                        min="0" max="100" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold_d">D (Nilai Minimum)</label>
                                    <input type="number" class="form-control" id="threshold_d" name="threshold_d"
                                        value="{{ old('threshold_d', $config->grade_thresholds['D'] ?? 30) }}" min="0"
                                        max="100" step="0.01" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Konfigurasi</button>
                        <div class="small text-muted mt-2">Total bobot (Absensi + Penilaian + Tugas) harus = 100</div>
                    </form>

                    <hr>

                    <form action="{{ route('admin.grades.calculate') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="btn btn-success">Hitung Nilai untuk Semua Siswa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
