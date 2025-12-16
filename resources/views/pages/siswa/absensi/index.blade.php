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
                        </div>
                        <div class="card-body">
                            @include('partials.alert')

                            @if($masterAbsensis->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-absensi-siswa">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Kelas</th>
                                                <th>Mata Pelajaran</th>
                                                <th>Guru</th>
                                                <th>Pertemuan</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($masterAbsensis as $masterAbsensi)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $masterAbsensi->tanggal->format('d/m/Y') }}</td>
                                                    <td>{{ $masterAbsensi->kelas->nama_kelas ?? '-' }}</td>
                                                    <td>{{ $masterAbsensi->mapel->nama_mapel ?? '-' }}</td>
                                                    <td>{{ $masterAbsensi->guru->nama ?? '-' }}</td>
                                                    <td>{{ $masterAbsensi->pertemuan ?? '-' }}</td>
                                                    <td>
                                                        @if(isset($studentAttendances[$masterAbsensi->id]))
                                                            @php
                                                                $attendance = $studentAttendances[$masterAbsensi->id];
                                                            @endphp
                                                            @if($attendance->status == 'hadir')
                                                                <span class="badge badge-success">Hadir</span>
                                                            @elseif($attendance->status == 'izin')
                                                                <span class="badge badge-warning">Izin</span>
                                                            @elseif($attendance->status == 'sakit')
                                                                <span class="badge badge-info">Sakit</span>
                                                            @endif

                                                            @if($attendance->is_teacher_validated)
                                                                <span class="badge badge-primary">Divalidasi</span>
                                                            @endif
                                                        @else
                                                            <span class="badge badge-secondary">Belum Absen</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($studentAttendances[$masterAbsensi->id]))
                                                            @php
                                                                $attendance = $studentAttendances[$masterAbsensi->id];
                                                            @endphp
                                                            @if(!$attendance->is_teacher_validated)
                                                                <!-- Edit buttons -->
                                                                <form method="POST" action="{{ route('absensi.siswa.submit', $masterAbsensi->id) }}" style="display: inline;">
                                                                    @csrf
                                                                    <input type="hidden" name="status" value="hadir">
                                                                    <button type="submit" class="btn btn-success btn-sm">
                                                                        <i class="fas fa-check"></i> Hadir
                                                                    </button>
                                                                </form>

                                                                <form method="POST" action="{{ route('absensi.siswa.submit', $masterAbsensi->id) }}" style="display: inline;">
                                                                    @csrf
                                                                    <input type="hidden" name="status" value="izin">
                                                                    <button type="submit" class="btn btn-warning btn-sm">
                                                                        <i class="fas fa-file-alt"></i> Izin
                                                                    </button>
                                                                </form>

                                                                <form method="POST" action="{{ route('absensi.siswa.submit', $masterAbsensi->id) }}" style="display: inline;">
                                                                    @csrf
                                                                    <input type="hidden" name="status" value="sakit">
                                                                    <button type="submit" class="btn btn-info btn-sm">
                                                                        <i class="fas fa-file-medical"></i> Sakit
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="text-muted">Divalidasi</span>
                                                            @endif
                                                        @else
                                                            <!-- Direct attendance buttons -->
                                                            <form method="POST" action="{{ route('absensi.siswa.submit', $masterAbsensi->id) }}" style="display: inline;">
                                                                @csrf
                                                                <input type="hidden" name="status" value="hadir">
                                                                <button type="submit" class="btn btn-success btn-sm">
                                                                    <i class="fas fa-check"></i> Hadir
                                                                </button>
                                                            </form>

                                                            <form method="POST" action="{{ route('absensi.siswa.submit', $masterAbsensi->id) }}" style="display: inline;">
                                                                @csrf
                                                                <input type="hidden" name="status" value="izin">
                                                                <button type="submit" class="btn btn-warning btn-sm">
                                                                    <i class="fas fa-file-alt"></i> Izin
                                                                </button>
                                                            </form>

                                                            <form method="POST" action="{{ route('absensi.siswa.submit', $masterAbsensi->id) }}" style="display: inline;">
                                                                @csrf
                                                                <input type="hidden" name="status" value="sakit">
                                                                <button type="submit" class="btn btn-info btn-sm">
                                                                    <i class="fas fa-file-medical"></i> Sakit
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center">
                                    {{ $masterAbsensis->links() }}
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Belum ada data absensi yang tersedia.
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
<script>
    $(document).ready(function() {
        // Initialize DataTable for student attendance
        if ($.fn.DataTable.isDataTable('#table-absensi-siswa')) {
            $('#table-absensi-siswa').DataTable().destroy();
        }

        $('#table-absensi-siswa').DataTable({
            columnDefs: [{ sortable: false, targets: [7] }], // Disable sorting on action column
            pageLength: 10,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang cocok",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya",
                },
            },
        });
    });
</script>
@endpush
