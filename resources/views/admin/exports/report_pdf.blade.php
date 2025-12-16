<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi Karyawan</title>
    {{-- Menggunakan style inline adalah praktik yang baik untuk PDF generator seperti DomPDF --}}
    <style>
        body { 
            font-family: sans-serif; 
            font-size: 10px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }
        th, td { 
            border: 1px solid #000; 
            padding: 5px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2; 
            text-align: center; 
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        h1 {
            font-size: 16px;
        }
        h3 {
            font-size: 12px;
            font-style: italic;
        }
        .period { 
            margin-bottom: 10px; 
            font-style: italic; 
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN REKAPITULASI ABSENSI KARYAWAN</h1>
        <h3>Periode: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 25%;">Nama Karyawan</th>
                <th style="width: 15%;">Total Hadir Tepat Waktu</th>
                <th style="width: 10%;">Total Terlambat</th>
                <th style="width: 10%;">Total Alpha</th>
                <th style="width: 15%;">Total Izin/Sakit/Cuti</th>
                <th style="width: 20%;">Total Lembur (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            {{-- $absences berisi data yang sudah di-groupBy('user_id') --}}
            @forelse ($absences as $user_id => $user_absences)
                @php
                    $total_hadir = $user_absences->where('status', 'tepat waktu')->count();
                    $total_terlambat = $user_absences->where('status', 'terlambat')->count();
                    $total_alpha = $user_absences->where('status', 'alpha')->count();
                    $total_izin = $user_absences->whereIn('status', ['izin', 'sakit', 'cuti'])->count();
                    
                    // Menjumlahkan semua kolom overtime_pay
                    $total_overtime_pay = $user_absences->sum('overtime_pay');
                    
                    // Ambil nama karyawan dari record pertama
                    $user_name = $user_absences->first()->user->name ?? 'N/A';
                @endphp
                <tr>
                    <td align="center">{{ $no++ }}</td>
                    <td>{{ $user_name }}</td>
                    <td align="center">{{ $total_hadir }} hari</td>
                    <td align="center">{{ $total_terlambat }} hari</td>
                    <td align="center">{{ $total_alpha }} hari</td>
                    <td align="center">{{ $total_izin }} hari</td>
                    <td align="right">Rp {{ number_format($total_overtime_pay, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" align="center">Tidak ada data absensi dalam periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>