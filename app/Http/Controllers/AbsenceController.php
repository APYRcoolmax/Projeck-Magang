<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absence;
use Carbon\Carbon;

class AbsenceController extends Controller
{
    /**
     * Halaman utama absensi user.
     */
    public function index()
    {
        $user = auth()->user();
        $today = now('Asia/Jakarta')->toDateString();

        $todayRecord = Absence::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $history = Absence::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('absence.index', compact('todayRecord', 'history'));
    }

    /**
     * Check-in user.
     */
    public function checkIn()
    {
        $user = auth()->user();
        $today = now('Asia/Jakarta')->toDateString();
        $currentTime = now('Asia/Jakarta')->format('H:i:s');

        $record = Absence::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($record && $record->time_in) {
            return back()->with('error', 'Sudah absen masuk hari ini.');
        }

        // Tentukan status otomatis
        $currentHour = now('Asia/Jakarta')->format('H:i');
        if ($currentHour <= '09:00') {
            $status = 'Tepat Waktu';
        } else {
            $status = 'Terlambat';
        }

        Absence::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            [
                'time_in' => $currentTime,
                'status' => $status,
            ]
        );

        return back()->with('success', "Check-in berhasil! Status: $status");
    }

    /**
     * Check-out user.
     */
    public function checkOut()
    {
        $user = auth()->user();
        $today = now('Asia/Jakarta')->toDateString();
        $currentTime = now('Asia/Jakarta')->format('H:i:s');

        $record = Absence::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$record || !$record->time_in) {
            return back()->with('error', 'Belum check-in.');
        }

        if ($record->time_out) {
            return back()->with('error', 'Sudah check-out hari ini.');
        }

        $record->update(['time_out' => $currentTime]);

        return back()->with('success', 'Check-out berhasil!');
    }

    /**
     * Rekap absensi bulanan.
     */
    public function summary(Request $request)
    {
        $user = auth()->user();
        $month = $request->input('month', now('Asia/Jakarta')->month);
        $year = $request->input('year', now('Asia/Jakarta')->year);

        $records = Absence::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $summary = [
            'tepat_waktu' => $records->where('status', 'Tepat Waktu')->count(),
            'terlambat'   => $records->where('status', 'Terlambat')->count(),
            'izin'        => $records->where('status', 'Izin')->count(),
            'sakit'       => $records->where('status', 'Sakit')->count(),
            'alpha'       => $records->where('status', 'Alpha')->count(),
        ];

        return view('absence.summary', compact('summary', 'month', 'year'));
    }
}
