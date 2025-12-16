<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Nilai Siswa</title>
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
    <h2>Rekap Nilai Siswa</h2>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>NIS</th>
                <th>Absensi</th>
                <th>Penilaian</th>
                <th>Nilai Akhir</th>
                <th>Grade</th>
                <th>Dihitung</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $r)
            <tr>
                <td>{{ optional($r->user)->name }}</td>
                <td>{{ optional($r->user)->email }}</td>
                <td>{{ optional($r->user)->nis }}</td>
                <td>{{ number_format((float) ($r->attendance_score ?? 0), 2) }}</td>
                <td>{{ number_format((float) ($r->assessment_score ?? 0), 2) }}</td>
                <td>{{ number_format((float) ($r->final_score ?? 0), 2) }}</td>
                <td>{{ $r->letter_grade ?? '-' }}</td>
                <td>{{ optional($r->calculated_at ?? $r->updated_at)->format('d-m-Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
