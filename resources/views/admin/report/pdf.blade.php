<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #888; padding: 6px; }
        th { background: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>

<h2>Laporan Absensi</h2>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Tanggal</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($absences as $i => $a)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $a->user->name ?? '-' }}</td>
                <td>{{ $a->date }}</td>
                <td>{{ $a->time_in ?? '-' }}</td>
                <td>{{ $a->time_out ?? '-' }}</td>
                <td>{{ $a->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
