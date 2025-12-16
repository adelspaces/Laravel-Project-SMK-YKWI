@extends('layouts.main')
@section('title', 'List Kelas')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Kelas</h4>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                            <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Data Kelas
                        </button>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kelas</th>
                                        <th>Wali Kelas</th>
                                        <th>Jurusan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kelas as $data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->nama_kelas }}</td>
                                        <td>{{ $data->guru->nama }}</td>
                                        <td>{{ $data->jurusan->nama_jurusan ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('kelas.edit', $data->id) }}" class="btn btn-success btn-sm mr-2">
                                                    <i class="nav-icon fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('kelas.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete' style="margin-left: 8px"><i class="nav-icon fas fa-trash-alt"></i> &nbsp; Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- MODAL TAMBAH --}}
                <div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Tambah Kelas</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('kelas.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if ($errors->any())
                                            <div class="alert alert-danger alert-dismissible show fade">
                                                <div class="alert-body">
                                                    <button class="close" data-dismiss="alert">
                                                        <span>&times;</span>
                                                    </button>
                                                    @foreach ($errors->all() as $error)
                                                        {{ $error }}<br>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                            <div class="form-group">
                                                <label for="nama_kelas">Nama Kelas</label>
                                                <input type="text" id="nama_kelas" name="nama_kelas" class="form-control @error('nama_kelas') is-invalid @enderror" placeholder="Nama Kelas">
                                            </div>
                                            <div class="form-group">
                                                <label for="guru_id">Wali Kelas</label>
                                                <select id="guru_id" name="guru_id" class="select2 form-control">
                                                    <option value="">-- Pilih Wali Kelas --</option>
                                                    @foreach ($gurus as $data)
                                                    <option value="{{ $data->id }}">{{ $data->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="jurusan_id">Jurusan</label>
                                                <select id="jurusan_id" name="jurusan_id" class="select2 form-control">
                                                    <option value="">-- Pilih Jurusan --</option>
                                                    @foreach ($jurusans as $data)
                                                    <option value="{{ $data->id }}">{{ $data->nama_jurusan }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-whitesmoke br">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- END MODAL --}}
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
