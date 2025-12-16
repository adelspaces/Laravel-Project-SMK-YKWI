@extends('layouts.main')
@section('title', 'Preview Import Soal')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Preview Import Soal - {{ $mapel->nama_mapel }}</h4>
                            <a href="{{ route('banksoal.import_questions') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if (!$result['success'])
                                <div class="alert alert-danger">
                                    <h5>Terdapat Error dalam File:</h5>
                                    <ul class="mb-0">
                                        @foreach ($result['errors'] as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <a href="{{ route('banksoal.import_questions') }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Perbaiki File
                                </a>
                            @else
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> File berhasil divalidasi!
                                    Ditemukan <strong>{{ $result['total_questions'] }}</strong> soal yang dapat diimpor.
                                </div>

                                @if (count($result['preview']) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="table-2">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Pertanyaan</th>
                                                    <th>Tipe</th>
                                                    <th>Opsi A</th>
                                                    <th>Opsi B</th>
                                                    <th>Opsi C</th>
                                                    <th>Opsi D</th>
                                                    <th>Opsi E</th>
                                                    <th>Bobot Nilai</th>
                                                    <th>Tingkat Kesulitan</th>
                                                    <th>Kunci Jawaban</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($result['preview'] as $index => $question)
                                                    <tr id="question-row-{{ $index }}">
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <span class="question-text">{{ Str::limit($question['pertanyaan'], 50) }}</span>
                                                            <textarea class="form-control question-edit d-none" rows="3">{{ $question['pertanyaan'] }}</textarea>
                                                        </td>
                                                        <td>
                                                            <span class="question-type">{{ ucfirst(str_replace('_', ' ', $question['tipe_soal'])) }}</span>
                                                            <select class="form-control type-edit d-none">
                                                                <option value="pilihan_ganda" {{ $question['tipe_soal'] == 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda</option>
                                                                <option value="esai" {{ $question['tipe_soal'] == 'esai' ? 'selected' : '' }}>Esai</option>
                                                                <option value="benar_salah" {{ $question['tipe_soal'] == 'benar_salah' ? 'selected' : '' }}>Benar/Salah</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <span>{{ $question['opsi_a'] ?? '-' }}</span>
                                                            <input type="text" class="form-control option-edit d-none" value="{{ $question['opsi_a'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <span>{{ $question['opsi_b'] ?? '-' }}</span>
                                                            <input type="text" class="form-control option-edit d-none" value="{{ $question['opsi_b'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <span>{{ $question['opsi_c'] ?? '-' }}</span>
                                                            <input type="text" class="form-control option-edit d-none" value="{{ $question['opsi_c'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <span>{{ $question['opsi_d'] ?? '-' }}</span>
                                                            <input type="text" class="form-control option-edit d-none" value="{{ $question['opsi_d'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <span>{{ $question['opsi_e'] ?? '-' }}</span>
                                                            <input type="text" class="form-control option-edit d-none" value="{{ $question['opsi_e'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <span>{{ $question['bobot_nilai'] ?? '10' }}</span>
                                                            <input type="number" class="form-control bobot-edit d-none" value="{{ $question['bobot_nilai'] ?? '10' }}" min="1" max="100">
                                                        </td>
                                                        <td>
                                                            <span>{{ $question['tingkat_kesulitan'] ?? 'sedang' }}</span>
                                                            <select class="form-control difficulty-edit d-none">
                                                                <option value="mudah" {{ ($question['tingkat_kesulitan'] ?? 'sedang') == 'mudah' ? 'selected' : '' }}>Mudah</option>
                                                                <option value="sedang" {{ ($question['tingkat_kesulitan'] ?? 'sedang') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                                                <option value="sulit" {{ ($question['tingkat_kesulitan'] ?? 'sedang') == 'sulit' ? 'selected' : '' }}>Sulit</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            @if ($question['tipe_soal'] == 'pilihan_ganda')
                                                                <strong>{{ strtoupper($question['jawaban_benar']) }}</strong>
                                                            @else
                                                                {{ Str::limit($question['kunci_jawaban'], 30) }}
                                                            @endif
                                                            <input type="text" class="form-control answer-edit d-none" value="{{ $question['tipe_soal'] == 'pilihan_ganda' ? $question['jawaban_benar'] : $question['kunci_jawaban'] }}">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-primary edit-btn" onclick="editQuestion({{ $index }})">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-success save-btn d-none" onclick="saveQuestion({{ $index }})">
                                                                <i class="fas fa-save"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-secondary cancel-btn d-none" onclick="cancelEdit({{ $index }})">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteQuestion({{ $index }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Add New Question Button -->
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-primary" onclick="addNewQuestion()">
                                            <i class="fas fa-plus"></i> Tambah Soal Baru
                                        </button>
                                    </div>

                                    <form id="import-form" action="{{ route('banksoal.confirm_import') }}" method="POST">
                                        @csrf
                                        <!-- Hidden input to store modified questions -->
                                        <input type="hidden" name="modified_questions" id="modified-questions" value="">
                                        <input type="hidden" name="deleted_questions" id="deleted-questions" value="">
                                        <input type="hidden" name="new_questions" id="new-questions" value="">

                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Klik "Konfirmasi Import" untuk mengimpor {{ $result['total_questions'] }} soal ke dalam bank soal.
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('banksoal.cancel_import') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Batal
                                            </a>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check"></i> Konfirmasi Import ({{ $result['total_questions'] }} soal)
                                            </button>
                                        </div>
                                    </form>
                                @endif
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
    // Store original questions data
    let originalQuestions = @json($result['preview']);
    let modifiedQuestions = {};
    let deletedQuestions = [];
    let newQuestions = [];
    let questionCounter = {{ count($result['preview']) }};

    // Function to edit a question
    function editQuestion(index) {
        const row = document.getElementById(`question-row-${index}`);
        // Hide display elements
        row.querySelectorAll('span, strong').forEach(el => el.classList.add('d-none'));
        // Show edit elements
        row.querySelectorAll('.question-edit, .type-edit, .option-edit, .bobot-edit, .difficulty-edit, .answer-edit, .save-btn, .cancel-btn').forEach(el => el.classList.remove('d-none'));
        // Hide edit button
        row.querySelector('.edit-btn').classList.add('d-none');
    }

    // Function to save edited question
    function saveQuestion(index) {
        const row = document.getElementById(`question-row-${index}`);

        // Get updated values
        const pertanyaan = row.querySelector('.question-edit').value;
        const tipe_soal = row.querySelector('.type-edit').value;
        const opsi_a = row.querySelectorAll('.option-edit')[0].value;
        const opsi_b = row.querySelectorAll('.option-edit')[1].value;
        const opsi_c = row.querySelectorAll('.option-edit')[2].value;
        const opsi_d = row.querySelectorAll('.option-edit')[3].value;
        const opsi_e = row.querySelectorAll('.option-edit')[4].value;
        const bobot_nilai = row.querySelector('.bobot-edit').value;
        const tingkat_kesulitan = row.querySelector('.difficulty-edit').value;
        const jawaban = row.querySelector('.answer-edit').value;

        // Update display elements
        row.querySelector('.question-text').textContent = pertanyaan.substring(0, 50) + (pertanyaan.length > 50 ? '...' : '');
        row.querySelector('.question-type').textContent = tipe_soal.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

        // Update options display
        row.querySelectorAll('td')[3].querySelector('span').textContent = opsi_a || '-';
        row.querySelectorAll('td')[4].querySelector('span').textContent = opsi_b || '-';
        row.querySelectorAll('td')[5].querySelector('span').textContent = opsi_c || '-';
        row.querySelectorAll('td')[6].querySelector('span').textContent = opsi_d || '-';
        row.querySelectorAll('td')[7].querySelector('span').textContent = opsi_e || '-';

        // Update bobot and difficulty
        row.querySelectorAll('td')[8].querySelector('span').textContent = bobot_nilai;
        row.querySelectorAll('td')[9].querySelector('span').textContent = tingkat_kesulitan;

        // Update answer display
        if (tipe_soal === 'pilihan_ganda') {
            row.querySelectorAll('td')[10].innerHTML = `<strong>${jawaban.toUpperCase()}</strong>`;
        } else {
            row.querySelectorAll('td')[10].innerHTML = jawaban.substring(0, 30) + (jawaban.length > 30 ? '...' : '');
        }

        // Hide edit elements
        row.querySelectorAll('.question-edit, .type-edit, .option-edit, .bobot-edit, .difficulty-edit, .answer-edit, .save-btn, .cancel-btn').forEach(el => el.classList.add('d-none'));
        // Show display elements
        row.querySelectorAll('span, strong').forEach(el => el.classList.remove('d-none'));
        // Show edit button
        row.querySelector('.edit-btn').classList.remove('d-none');

        // Store modified question
        modifiedQuestions[index] = {
            pertanyaan: pertanyaan,
            tipe_soal: tipe_soal,
            opsi_a: opsi_a,
            opsi_b: opsi_b,
            opsi_c: opsi_c,
            opsi_d: opsi_d,
            opsi_e: opsi_e,
            bobot_nilai: bobot_nilai,
            tingkat_kesulitan: tingkat_kesulitan,
            jawaban_benar: tipe_soal === 'pilihan_ganda' ? jawaban.toUpperCase() : null,
            kunci_jawaban: tipe_soal !== 'pilihan_ganda' ? jawaban : null
        };
    }

    // Function to cancel edit
    function cancelEdit(index) {
        const row = document.getElementById(`question-row-${index}`);
        // Hide edit elements
        row.querySelectorAll('.question-edit, .type-edit, .option-edit, .bobot-edit, .difficulty-edit, .answer-edit, .save-btn, .cancel-btn').forEach(el => el.classList.add('d-none'));
        // Show display elements
        row.querySelectorAll('span, strong').forEach(el => el.classList.remove('d-none'));
        // Show edit button
        row.querySelector('.edit-btn').classList.remove('d-none');
    }

    // Function to delete a question
    function deleteQuestion(index) {
        if (confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
            const row = document.getElementById(`question-row-${index}`);
            row.remove();

            // Add to deleted questions
            if (!originalQuestions[index].hasOwnProperty('is_new')) {
                deletedQuestions.push(index);
            } else {
                // If it's a new question, remove it from newQuestions array
                newQuestions = newQuestions.filter(q => q.temp_index !== index);
            }

            // Update question numbers
            updateQuestionNumbers();
        }
    }

    // Function to add a new question
    function addNewQuestion() {
        const tableBody = document.querySelector('#table-2 tbody');
        const newIndex = questionCounter++;

        const newRow = document.createElement('tr');
        newRow.id = `question-row-${newIndex}`;
        newRow.innerHTML = `
            <td>${newIndex + 1}</td>
            <td>
                <span class="question-text"></span>
                <textarea class="form-control question-edit" rows="3"></textarea>
            </td>
            <td>
                <span class="question-type">Pilihan Ganda</span>
                <select class="form-control type-edit">
                    <option value="pilihan_ganda" selected>Pilihan Ganda</option>
                    <option value="esai">Esai</option>
                    <option value="benar_salah">Benar/Salah</option>
                </select>
            </td>
            <td>
                <span>-</span>
                <input type="text" class="form-control option-edit" value="">
            </td>
            <td>
                <span>-</span>
                <input type="text" class="form-control option-edit" value="">
            </td>
            <td>
                <span>-</span>
                <input type="text" class="form-control option-edit" value="">
            </td>
            <td>
                <span>-</span>
                <input type="text" class="form-control option-edit" value="">
            </td>
            <td>
                <span>-</span>
                <input type="text" class="form-control option-edit" value="">
            </td>
            <td>
                <span>10</span>
                <input type="number" class="form-control bobot-edit" value="10" min="1" max="100">
            </td>
            <td>
                <span>sedang</span>
                <select class="form-control difficulty-edit">
                    <option value="mudah">Mudah</option>
                    <option value="sedang" selected>Sedang</option>
                    <option value="sulit">Sulit</option>
                </select>
            </td>
            <td>
                <strong></strong>
                <input type="text" class="form-control answer-edit" value="">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-primary edit-btn d-none" onclick="editQuestion(${newIndex})">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-success save-btn" onclick="saveNewQuestion(${newIndex})">
                    <i class="fas fa-save"></i>
                </button>
                <button type="button" class="btn btn-sm btn-secondary cancel-btn d-none" onclick="cancelEdit(${newIndex})">
                    <i class="fas fa-times"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="deleteQuestion(${newIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tableBody.appendChild(newRow);

        // Add to new questions array
        const newQuestion = {
            temp_index: newIndex,
            pertanyaan: '',
            tipe_soal: 'pilihan_ganda',
            opsi_a: '',
            opsi_b: '',
            opsi_c: '',
            opsi_d: '',
            opsi_e: '',
            bobot_nilai: 10,
            tingkat_kesulitan: 'sedang',
            jawaban_benar: '',
            kunci_jawaban: '',
            is_new: true
        };

        newQuestions.push(newQuestion);

        // Show edit mode for new question
        editQuestion(newIndex);
    }

    // Function to save a new question
    function saveNewQuestion(index) {
        const row = document.getElementById(`question-row-${index}`);

        // Get values
        const pertanyaan = row.querySelector('.question-edit').value;
        const tipe_soal = row.querySelector('.type-edit').value;
        const opsi_a = row.querySelectorAll('.option-edit')[0].value;
        const opsi_b = row.querySelectorAll('.option-edit')[1].value;
        const opsi_c = row.querySelectorAll('.option-edit')[2].value;
        const opsi_d = row.querySelectorAll('.option-edit')[3].value;
        const opsi_e = row.querySelectorAll('.option-edit')[4].value;
        const bobot_nilai = row.querySelector('.bobot-edit').value;
        const tingkat_kesulitan = row.querySelector('.difficulty-edit').value;
        const jawaban = row.querySelector('.answer-edit').value;

        // Update display
        row.querySelector('.question-text').textContent = pertanyaan.substring(0, 50) + (pertanyaan.length > 50 ? '...' : '');
        row.querySelector('.question-type').textContent = tipe_soal.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        row.querySelectorAll('td')[3].querySelector('span').textContent = opsi_a || '-';
        row.querySelectorAll('td')[4].querySelector('span').textContent = opsi_b || '-';
        row.querySelectorAll('td')[5].querySelector('span').textContent = opsi_c || '-';
        row.querySelectorAll('td')[6].querySelector('span').textContent = opsi_d || '-';
        row.querySelectorAll('td')[7].querySelector('span').textContent = opsi_e || '-';
        row.querySelectorAll('td')[8].querySelector('span').textContent = bobot_nilai;
        row.querySelectorAll('td')[9].querySelector('span').textContent = tingkat_kesulitan;

        if (tipe_soal === 'pilihan_ganda') {
            row.querySelectorAll('td')[10].innerHTML = `<strong>${jawaban.toUpperCase()}</strong>`;
        } else {
            row.querySelectorAll('td')[10].innerHTML = jawaban.substring(0, 30) + (jawaban.length > 30 ? '...' : '');
        }

        // Hide edit elements
        row.querySelectorAll('.question-edit, .type-edit, .option-edit, .bobot-edit, .difficulty-edit, .answer-edit, .save-btn, .cancel-btn').forEach(el => el.classList.add('d-none'));
        // Show display elements
        row.querySelectorAll('span, strong').forEach(el => el.classList.remove('d-none'));
        // Show edit button
        row.querySelector('.edit-btn').classList.remove('d-none');

        // Update new question in array
        const questionIndex = newQuestions.findIndex(q => q.temp_index === index);
        if (questionIndex !== -1) {
            newQuestions[questionIndex] = {
                ...newQuestions[questionIndex],
                pertanyaan: pertanyaan,
                tipe_soal: tipe_soal,
                opsi_a: opsi_a,
                opsi_b: opsi_b,
                opsi_c: opsi_c,
                opsi_d: opsi_d,
                opsi_e: opsi_e,
                bobot_nilai: parseInt(bobot_nilai),
                tingkat_kesulitan: tingkat_kesulitan,
                jawaban_benar: tipe_soal === 'pilihan_ganda' ? jawaban.toUpperCase() : null,
                kunci_jawaban: tipe_soal !== 'pilihan_ganda' ? jawaban : null
            };
        }
    }

    // Function to update question numbers
    function updateQuestionNumbers() {
        const rows = document.querySelectorAll('#table-2 tbody tr');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
    }

    // Handle form submission
    document.getElementById('import-form').addEventListener('submit', function(e) {
        // Update hidden inputs with JSON data
        document.getElementById('modified-questions').value = JSON.stringify(modifiedQuestions);
        document.getElementById('deleted-questions').value = JSON.stringify(deletedQuestions);
        document.getElementById('new-questions').value = JSON.stringify(newQuestions);
    });
</script>
@endpush
