<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #555; padding: 6px; text-align: center; }
        th { background-color: #f0f0f0; }
        h2 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Laporan Absensi Karyawan</h2>
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
                <td>{{ $i+1 }}</td>
                <td>{{ $a->user->name }}</td>
                <td>{{ $a->date->format('d M Y') }}</td>
                <td>{{ $a->time_in ?? '-' }}</td>
                <td>{{ $a->time_out ?? '-' }}</td>
                <td>{{ ucfirst($a->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
