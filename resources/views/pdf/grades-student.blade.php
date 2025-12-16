<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Nilai Siswa</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        h2 {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h2>Nilai Siswa</h2>
    <p>
        <strong>Nama:</strong> {{ $student->name ?? '-' }}<br>
        <strong>Email:</strong> {{ $student->email ?? '-' }}<br>
        <strong>NIS:</strong> {{ $student->nis ?? '-' }}
    </p>
    @if($result)
    <table>
        <thead>
            <tr>
                <th>Komponen</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Absensi</td>
                <td>{{ number_format((float) ($result->attendance_score ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td>Penilaian</td>
                <td>{{ number_format((float) ($result->assessment_score ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td>Nilai Akhir</td>
                <td>{{ number_format((float) ($result->final_score ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td>Grade</td>
                <td>{{ $result->letter_grade ?? '-' }}</td>
            </tr>
            <tr>
                <td>Dihitung</td>
                <td>{{ optional($result->calculated_at ?? $result->updated_at)->format('d-m-Y H:i') }}</td>
            </tr>
        </tbody>
    </table>
    @else
    <p>Belum ada hasil nilai untuk siswa ini.</p>
    @endif
</body>

</html>
