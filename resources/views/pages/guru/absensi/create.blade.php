@extends('layouts.main')
@section('title', 'Input Absensi')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Input Absensi</h4>
                            <div class="card-header-action">
                                <a href="{{ route('absensi.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')

                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Sistem Absensi Baru</h5>
                                <p>Sistem absensi telah diperbarui. Silakan gunakan sistem Master Absensi untuk membuat dan mengelola absensi.</p>
                                <a href="{{ route('absensi.master.index') }}" class="btn btn-primary">
                                    <i class="fas fa-list"></i> Ke Master Absensi
                                </a>
                                <a href="{{ route('absensi.master.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Buat Master Absensi Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jadwalSelect = document.getElementById('jadwal_id');
    const studentsContainer = document.getElementById('students-container');
    const kelasInput = document.getElementById('kelas_id');
    const mapelInput = document.getElementById('mapel_id');
    const tanggalInput = document.getElementById('tanggal');

    jadwalSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const kelasId = selectedOption.dataset.kelas;
            const mapelId = selectedOption.dataset.mapel;
            const tanggal = tanggalInput.value;

            kelasInput.value = kelasId;
            mapelInput.value = mapelId;

            loadStudents(kelasId, mapelId, tanggal);
        } else {
            studentsContainer.style.display = 'none';
        }
    });
});

function loadStudents(kelasId, mapelId, tanggal) {
    fetch(`{{ route('absensi.students-by-class') }}?kelas_id=${kelasId}&mapel_id=${mapelId}&tanggal=${tanggal}`)
        .then(response => response.json())
        .then(data => {
            const studentsList = document.getElementById('students-list');
            studentsList.innerHTML = '';

            data.students.forEach((student, index) => {
                const existingStatus = data.existing_attendance[student.id] || 'hadir';

                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${student.nama}</td>
                        <td>
                            <select name="absen[${student.id}]" class="form-control form-control-sm" required>
                                <option value="hadir" ${existingStatus === 'hadir' ? 'selected' : ''}>Hadir</option>
                                <option value="izin" ${existingStatus === 'izin' ? 'selected' : ''}>Izin</option>
                                <option value="sakit" ${existingStatus === 'sakit' ? 'selected' : ''}>Sakit</option>
                                <option value="alfa" ${existingStatus === 'alfa' ? 'selected' : ''}>Alfa</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="keterangan[${student.id}]"
                                   class="form-control form-control-sm"
                                   placeholder="Keterangan (opsional)">
                        </td>
                    </tr>
                `;
                studentsList.innerHTML += row;
            });

            document.getElementById('students-container').style.display = 'block';
        })
        .catch(error => {
            console.error('Error loading students:', error);
            alert('Gagal memuat data siswa. Silakan coba lagi.');
        });
}

function setAllStatus(status) {
    const selects = document.querySelectorAll('select[name^="absen["]');
    selects.forEach(function(select) {
        select.value = status;
        select.classList.add('border-primary');
        setTimeout(() => {
            select.classList.remove('border-primary');
        }, 1000);
    });
}
</script>
@endpush
