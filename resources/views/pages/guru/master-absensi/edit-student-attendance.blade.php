@extends('layouts.main')
@section('title', 'Validasi Absensi Siswa')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Validasi Absensi Siswa</h4>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')

                            <!-- Student Info -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5>Informasi Siswa</h5>
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Nama Siswa:</strong></td>
                                                    <td>{{ $absensiSiswa->siswa->nama ?? '-' }}</td>
                                                    <td><strong>Status yang Dipilih Siswa:</strong></td>
                                                    <td>
                                                        @if($absensiSiswa->status == 'hadir')
                                                            <span class="badge badge-success">Hadir</span>
                                                        @elseif($absensiSiswa->status == 'izin')
                                                            <span class="badge badge-warning">Izin</span>
                                                        @elseif($absensiSiswa->status == 'sakit')
                                                            <span class="badge badge-info">Sakit</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('absensi.master.update-student-attendance', $absensiSiswa->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="status">Status Kehadiran</label>
                                    <select name="status" id="status" class="form-control select2" required>
                                        <option value="">Pilih Status</option>
                                        <option value="hadir" {{ (old('status', $absensiSiswa->status) == 'hadir') ? 'selected' : '' }}>Hadir</option>
                                        <option value="izin" {{ (old('status', $absensiSiswa->status) == 'izin') ? 'selected' : '' }}>Izin</option>
                                        <option value="sakit" {{ (old('status', $absensiSiswa->status) == 'sakit') ? 'selected' : '' }}>Sakit</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Validasi</button>
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
        // Initialize Select2
        $('.select2').select2();
    });
</script>
@endpush
