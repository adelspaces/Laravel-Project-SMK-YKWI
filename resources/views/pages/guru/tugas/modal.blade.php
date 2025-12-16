<div class="modal fade" id="nilaiModal" tabindex="-1" role="dialog" aria-labelledby="nilaiModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="nilaiForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nilaiModalLabel">Beri Nilai</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nilaiInput" class="mb-1">Nilai</label>
                        <input type="number" name="nilai" id="nilaiInput" class="form-control" min="0" max="100"
                            step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openNilaiModal(id, nilai) {
        var form = document.getElementById('nilaiForm');
        var baseUrl = "{{ url('/tugas/jawaban') }}";
        form.setAttribute('action', baseUrl + '/' + id + '/nilai');
        var input = document.getElementById('nilaiInput');
        input.value = (nilai !== null && nilai !== undefined) ? nilai : '';
        if (typeof $ !== 'undefined' && $('#nilaiModal').modal) {
            $('#nilaiModal').modal('show');
        } else {
            document.getElementById('nilaiModal').style.display = 'block';
        }
    }
</script>
