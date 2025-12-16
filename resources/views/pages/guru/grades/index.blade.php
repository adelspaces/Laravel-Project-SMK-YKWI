@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Manajemen Nilai</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="mb-3">
                        <a href="{{ route('grades.calculate') }}" class="btn btn-success"
                            onclick="event.preventDefault(); document.getElementById('calculate-form').submit();">
                            Hitung Nilai untuk Semua Siswa
                        </a>
                        <a href="{{ route('grades.exportAll') }}" class="btn btn-outline-primary ml-2">
                            Export Semua Nilai (Excel)
                        </a>
                        <a href="{{ route('grades.exportAllPdf') }}" class="btn btn-outline-danger ml-2">
                            Export Semua Nilai (PDF)
                        </a>
                    </div>

                    <form id="calculate-form" action="{{ route('grades.calculate') }}" method="POST"
                        style="display: none;">
                        @csrf
                    </form>

                    <h5>Daftar Siswa</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        <a href="{{ route('grades.student', $student->id) }}"
                                            class="btn btn-sm btn-info">
                                            Lihat Nilai
                                        </a>
                                        <a href="{{ route('grades.exportStudent', $student->id) }}"
                                            class="btn btn-sm btn-secondary ml-1">
                                            Export
                                        </a>
                                        <a href="{{ route('grades.exportStudentPdf', $student->id) }}"
                                            class="btn btn-sm btn-dark ml-1">
                                            PDF
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
