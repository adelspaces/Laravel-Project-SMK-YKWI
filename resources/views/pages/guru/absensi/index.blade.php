@extends('layouts.main')
@section('title', 'Absensi')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Absensi</h4>
                            <div>
                                <a href="{{ route('absensi.statistics') }}" class="btn btn-primary">Statistik</a>
                                <a href="{{ route('absensi.master.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Buat Master Absensi
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')

                            <!-- Filter Form -->
                            <form action="{{ route('absensi.index') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="kelas_id">Kelas</label>
                                            <select name="kelas_id" id="kelas_id" class="form-control select2">
                                                <option value="">Semua Kelas</option>
                                                @foreach($kelas as $item)
                                                    <option value="{{ $item->id }}" {{ request('kelas_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama_kelas }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="mapel_id">Mata Pelajaran</label>
                                            <select name="mapel_id" id="mapel_id" class="form-control select2">
                                                <option value="">Semua Mata Pelajaran</option>
                                                @foreach($mapel as $item)
                                                    <option value="{{ $item->id }}" {{ request('mapel_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama_mapel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal</label>
                                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ request('tanggal') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control select2">
                                                <option value="">Semua Status</option>
                                                <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                                                <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                <option value="alfa" {{ request('status') == 'alfa' ? 'selected' : '' }}>Alfa</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="is_student_submitted">Status Pengisian</label>
                                            <select name="is_student_submitted" id="is_student_submitted" class="form-control select2">
                                                <option value="">Semua</option>
                                                <option value="1" {{ request('is_student_submitted') == '1' ? 'selected' : '' }}>Diisi Siswa</option>
                                                <option value="0" {{ request('is_student_submitted') == '0' ? 'selected' : '' }}>Diisi Guru</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group" style="margin-top: 30px;">
                                            <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                            <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @if($paginatedRecords->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-2">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Kelas</th>
                                                <th>Mata Pelajaran</th>
                                                <th>Pertemuan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($paginatedRecords as $record)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $record->tanggal->format('d/m/Y') }}</td>
                                                    <td>{{ $record->kelas->nama_kelas ?? '-' }}</td>
                                                    <td>{{ $record->mapel->nama_mapel ?? '-' }}</td>
                                                    <td>{{ $record->pertemuan ?? '-' }}</td>
                                                    <td>
                                                        <a href="{{ route('absensi.master.show-student-attendance', $record->id) }}" class="btn btn-info btn-sm">
                                                            <i class="fas fa-users"></i> Lihat Siswa
                                                        </a>
                                                        <form method="POST" action="{{ route('absensi.master.destroy', $record->id) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-danger btn-sm show_confirm" title='Delete'>
                                                                <i class="fas fa-trash"></i> Hapus
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center">
                                    {{ $paginatedRecords->links() }}
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Belum ada data absensi.
                                </div>
                            @endif
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
