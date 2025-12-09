<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absence;
use App\Models\DailyStatus;   // 🔹 Tambahan
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

        /**
         * =======================================================
         *  🔥 AUTO ALPHA — Jika lewat jam tertentu & belum absen
         * =======================================================
         */
        $autoTime = env('AUTO_ALPHA_TIME', '15.00'); 
        $currentTime = now('Asia/Jakarta')->format('H:i');

        $record = Absence::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($currentTime > $autoTime && !$record) {
            Absence::create([
                'user_id' => $user->id,
                'date'    => $today,
                'status'  => 'Alpha',
            ]);
        }

        /**
         * =======================================================
         *  Ambil record hari ini & riwayat
         * =======================================================
         */
        $todayRecord = Absence::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $history = Absence::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('absence.index', compact('todayRecord', 'history'));
    }


    /**
     * =======================================================
     *  Check-in user
     * =======================================================
     */
    public function checkIn()
    {
        $user = auth()->user();
        $today = now('Asia/Jakarta')->toDateString();
        $currentTime = now('Asia/Jakarta')->format('H:i:s');

        /**
         * 🔥 CEGAH ABSEN JIKA STATUS = izin / sakit / alpha
         */
        $dailyStatus = DailyStatus::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($dailyStatus && in_array(strtolower($dailyStatus->status), ['izin', 'sakit', 'alpha'])) {
            return back()->with('error', 'Anda tidak bisa check-in karena status hari ini: ' . ucfirst($dailyStatus->status));
        }

        // Sudah absen?
        $record = Absence::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($record && $record->time_in) {
            return back()->with('error', 'Sudah absen masuk hari ini.');
        }

        // Tentukan status otomatis
        $currentHour = now('Asia/Jakarta')->format('H:i');
        $status = ($currentHour <= '09:00') ? 'Tepat Waktu' : 'Terlambat';

        Absence::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            [
                'time_in' => $currentTime,
                'status'  => $status,
            ]
        );

        return back()->with('success', "Check-in berhasil! Status: $status");
    }


    /**
     * =======================================================
     *  Check-out user
     * =======================================================
     */
    public function checkOut()
    {
        $user = auth()->user();
        $today = now('Asia/Jakarta')->toDateString();
        $currentTime = now('Asia/Jakarta')->format('H:i:s');

        /**
         * 🔥 CEGAH CHECK-OUT jika status = izin / sakit / alpha
         */
        $dailyStatus = DailyStatus::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($dailyStatus && in_array(strtolower($dailyStatus->status), ['izin', 'sakit', 'alpha'])) {
            return back()->with('error', 'Anda tidak dapat check-out karena status Anda: ' . ucfirst($dailyStatus->status));
        }

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

        $overtimeRate = \App\Models\Overtime::where('user_id', $user->id)->first();

    if ($overtimeRate) {
        $endOfWork = Carbon::parse($today . ' 17:00:00');
        $checkoutTime = Carbon::parse($today . ' ' . $currentTime);

        if ($checkoutTime->gt($endOfWork)) {
            $hours = $checkoutTime->floatDiffInHours($endOfWork);

            $record->update([
                'overtime_hours' => $hours,
                'overtime_pay'   => round($hours * $overtimeRate->rate_per_hour)
            ]);
        }
    }

        return back()->with('success', 'Check-out berhasil!');
    }


    /**
     * Rekap absensi bulanan user
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
