@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Laporan Nilai Saya</h4>
                </div>
                <div class="card-body">
                    @if($gradeResult)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Nilai Saya</h5>
                                </div>
                                <div class="card-body text-center">
                                    <h1><span class="badge badge-primary">{{ $gradeResult->letter_grade }}</span></h1>
                                    <p class="lead">Nilai Akhir: {{ number_format($gradeResult->final_score, 2) }}</p>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Rincian Nilai</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th>Nilai Absensi:</th>
                                            <td>{{ number_format($gradeResult->attendance_score, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nilai Tugas:</th>
                                            <td>{{ number_format($gradeResult->assessment_score, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nilai Akhir:</th>
                                            <td>{{ number_format($gradeResult->final_score, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Skala Nilai</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Grade</th>
                                                <th>Nilai Minimum</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>A</td>
                                                <td>80</td>
                                            </tr>
                                            <tr>
                                                <td>A-</td>
                                                <td>75</td>
                                            </tr>
                                            <tr>
                                                <td>B+</td>
                                                <td>70</td>
                                            </tr>
                                            <tr>
                                                <td>B</td>
                                                <td>60</td>
                                            </tr>
                                            <tr>
                                                <td>B-</td>
                                                <td>55</td>
                                            </tr>
                                            <tr>
                                                <td>C+</td>
                                                <td>50</td>
                                            </tr>
                                            <tr>
                                                <td>C</td>
                                                <td>40</td>
                                            </tr>
                                            <tr>
                                                <td>C-</td>
                                                <td>35</td>
                                            </tr>
                                            <tr>
                                                <td>D</td>
                                                <td>30</td>
                                            </tr>
                                            <tr>
                                                <td>E</td>
                                                <td>Dibawah 30</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        Nilai Anda belum dihitung. Silakan hubungi administrator.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
