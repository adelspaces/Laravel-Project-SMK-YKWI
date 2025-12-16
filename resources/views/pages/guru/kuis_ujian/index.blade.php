@extends('layouts.main')
@section('title', 'Kuis & Ujian')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Pengaturan Kuis dan Ujian</h4>
                            <a href="{{ route('kuis_ujian.create') }}" class="btn btn-primary mb-3">
                                + Tambah Kuis/Ujian
                            </a>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul</th>
                                            <th>Tipe</th>
                                            <th>Waktu Mulai</th>
                                            <th>Waktu Selesai</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($kuisUjians as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->judul }}</td>
                                                <td>{{ ucfirst($item->tipe) }}</td>
                                                <td>{{ $item->waktu_mulai }}</td>
                                                <td>{{ $item->waktu_selesai }}</td>
                                                <td>{{ $item->mapel->nama_mapel }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('guru.kuis_ujian.hasil', $item->id) }}"
                                                            class="btn btn-sm btn-primary mr-1">
                                                            <i class="fas fa-eye"></i> Lihat Jawaban
                                                        </a>
                                                        <a href="{{ route('kuis_ujian.edit', $item->id) }}"
                                                            class="btn btn-sm btn-success mr-1" style="margin-right:5px;">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <form action="{{ route('kuis_ujian.destroy', $item->id) }}"
                                                            method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-danger show_confirm">
                                                                <i class="fas fa-trash-alt"></i> Hapus
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Belum ada data kuis/ujian</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
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
