@extends('layouts.main')
@section('title', 'Banksoals')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Manajemen Bank soal</h4>
                            <div class="btn-group">
                                <a href="{{ route('banksoal.create') }}" class="btn btn-primary mb-3">+ Tambah Soal</a>
                                <a href="{{ route('banksoal.import_questions') }}" class="btn btn-success mb-3 ml-2">
                                    <i class="fas fa-file-excel"></i> Import Excel
                                </a>
                                <a href="{{ route('banksoal.download_template') }}" class="btn btn-info mb-3 ml-2">
                                    <i class="fas fa-download"></i> Download Template
                                </a>
                            </div>
                        </div>
                        <div class="card-body">

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <table class="table table-bordered table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pertanyaan</th>
                                        <th>Tipe Soal</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Guru</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($banksoal as $soal)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ Str::limit($soal->pertanyaan, 50) }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $soal->tipe_soal)) }}</td>
                                            <td>{{ $soal->mapel->nama_mapel }}</td>
                                            <td>{{ $soal->guru->nama ?? '-' }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('banksoal.edit', $soal->id) }}"
                                                        class="btn btn-sm btn-success" style="margin-right:5px;">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <form action="{{ route('banksoal.destroy', $soal->id) }}" method="POST"
                                                        style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger show_confirm">
                                                            <i class="fas fa-trash-alt"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada soal</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script type="text/javascript">
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `Yakin ingin menghapus data ini?`,
                    text: "Data akan terhapus secara permanen!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    </script>
@endpush
