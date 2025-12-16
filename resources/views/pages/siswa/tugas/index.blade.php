@extends('layouts.main')
@section('title', 'Tugas')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Tugas</h4>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul Tugas</th>
                                        <th>Deskripsi</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tugas as $key => $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->judul }}</td>
                                        <td>{{ $item->deskripsi }}</td>
                                        <td>{{ $item->guru->mapel->nama_mapel }}</td>
                                        <td>
                                            <div class="d-flex ">
                                                {{-- Cek apakah siswa sudah mengirim jawaban --}}
                                                @php
                                                $siswa = App\Models\Siswa::where('nis', Auth::user()->nis)->first();
                                                $jawabanSiswa = $jawaban->where('tugas_id',
                                                $item->id)->where('siswa_id', $siswa->id)->first();
                                                @endphp

                                                @if ($jawabanSiswa)
                                                <span class="badge badge-success align-self-center">Sudah
                                                    Mengumpulkan</span>
                                                @else
                                                <a href="javascript:void(0)" class="btn btn-primary mr-2 btn-sm"
                                                    onclick="openJawabanModal({{ $item->id }})">
                                                    <i class="nav-icon fas fa-paper-plane"></i> Kirim Jawaban
                                                </a>
                                                @endif

                                                <a href="{{ route('siswa.tugas.download', $item->id) }}"
                                                    class="btn btn-success btn-sm align-self-center">
                                                    <i class="nav-icon fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada tugas</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Kirim Jawaban --}}
        <div class="modal fade" tabindex="-1" role="dialog" id="modalKirimJawaban">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('kirim-jawaban') }}" method="POST" enctype="multipart/form-data"
                        id="formKirimJawaban">
                        @csrf
                        <input type="hidden" name="_method" value="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Kirim Jawaban</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {{-- input hidden tugas_id akan diisi pakai javascript --}}
                            <input type="hidden" name="tugas_id" id="tugas_id" value="">

                            <div class="form-group">
                                <label for="jawaban">Jawaban</label>
                                <textarea id="jawaban" name="jawaban"
                                    class="form-control @error('jawaban') is-invalid @enderror"
                                    placeholder="Tulis jawaban Anda di sini" rows="4" required></textarea>
                                @error('jawaban')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="file">File Tugas</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input id="file" type="file" name="file"
                                            class="custom-file-input @error('file') is-invalid @enderror">
                                        <label class="custom-file-label" for="file">Pilih file</label>
                                    </div>
                                </div>
                                @error('file')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer br">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" id="btnKirimJawaban">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('script')
<script>
    window.openJawabanModal = function(tugasId) {
        if (!tugasId || tugasId <= 0) {
            alert('ID tugas tidak valid.');
            return;
        }

        $('#tugas_id').val(tugasId);

        $('#formKirimJawaban')[0].reset();

        $('.custom-file-label').text('Pilih file');

        $('input[name="tugas_id"]').val(tugasId);
        $('#tugas_id').attr('value', tugasId);

        $('#modalKirimJawaban').modal('show');
    };

    $(document).ready(function() {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        $('#formKirimJawaban').on('submit', function(e) {
            var tugasId = $('#tugas_id').val();

            if (!tugasId || tugasId === '') {
                e.preventDefault();
                alert('Tugas ID tidak tersedia. Silakan coba lagi.');
                return false;
            }

            $('input[name="tugas_id"]').val(tugasId);

            $('#btnKirimJawaban').prop('disabled', true).text('Mengirim...');
        });
    });
</script>
@endpush
