@extends('layouts.main')
@section('title', 'List User')

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
                        <h4>List User</h4>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"><i
                                class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Data User</button>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <!-- Fixed: Changed 'Roles' to 'Role' -->
                                        <th>No</th>
                                        <th>Nama User</th>
                                        <th>Role</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $result => $data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->name }}</td>
                                        <!-- Fixed: Changed 'roles' to 'role' -->
                                        <td>{{ $data->role }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <form method="POST" action="{{ route('user.destroy', $data->id) }}">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn btn-danger btn-sm show_confirm"
                                                        data-toggle="tooltip" title='Delete' style="margin-left: 8px"><i
                                                            class="nav-icon fas fa-trash-alt"></i> &nbsp;
                                                        Hapus</button>
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
            </div>
            <div class="modal fade" role="dialog" id="exampleModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('user.store') }}" method="POST">
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
                                                {{ $error }}
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="{{ __('Email User') }}" value="{{ old('email', '') }}">
                                        </div>
                                        <input name="password" type="password" value="password123" hidden>
                                        <div class="form-group">
                                            <!-- Fixed: Changed 'Roles' to 'Role' and 'roles' to 'role' -->
                                            <label for="role">Role</label>
                                            <select id="role" name="role"
                                                class="form-control @error('role') is-invalid @enderror">
                                                <option value="">-- Pilih Role --</option>
                                                <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>
                                                    Admin</option>
                                                <option value="guru" {{ old('role')=='guru' ? 'selected' : '' }}>
                                                    Guru</option>
                                                <option value="siswa" {{ old('role')=='siswa' ? 'selected' : '' }}>
                                                    Siswa</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="noId"></div>
                                    </div>
                                </div>
                                <div class="modal- br">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
<script>
    $(document).ready(function() {
            // Fixed: Changed 'roles' to 'role' in JavaScript
            $('#role').change(function() {
                var kel = $('#role option:selected').val();
                if (kel == "guru") {
                    $("#noId").html(
                        '<label for="nip">NIP guru</label><input id="nip" type="text" onkeypress="return inputAngka(event)" placeholder="NIP Guru" class="form-control" name="nip" value="{{ old('nip') }}" autocomplete="off">'
                    );
                } else if (kel == "siswa") {
                    $("#noId").html(
                        '<label for="nis">NIS Siswa</label><input id="nis" type="text" placeholder="NIS Siswa" class="form-control" name="nis" value="{{ old('nis') }}" autocomplete="off">'
                    );
                } else if (kel == "admin") {
                    $("#noId").html(
                        '<label for="name">Nama Admin</label><input id="name" type="text" placeholder="Nama admin" class="form-control" name="name" value="{{ old('name') }}" autocomplete="off">'
                    );
                } else {
                    $("#noId").html("");
                }
            });
        });
</script>
@endpush
