<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absence;
use App\Models\Overtime;
use App\Models\DailyStatus; 
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
         * 🔥 AUTO ALPHA — Jika lewat jam tertentu & belum absen
         * =======================================================
         */
        $autoTime = env('AUTO_ALPHA_TIME', '15:00'); // Diubah format H:i
        $currentTime = now('Asia/Jakarta')->format('H:i');

        $record = Absence::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($currentTime > $autoTime && (!$record || $record->status == null)) {
            // Cek juga jangan sampai status sudah diisi (misal Izin/Sakit)
            // Hanya Alpha jika memang belum ada record atau recordnya kosong/baru dibuat
            $checkExisting = Absence::where('user_id', $user->id)
                ->where('date', $today)
                ->whereNotNull('status') // Cek status sudah ada
                ->first();

            if (!$checkExisting) {
                Absence::updateOrCreate(
                    ['user_id' => $user->id, 'date' => $today],
                    ['status' => 'Alpha']
                );
            }
        }

        /**
         * =======================================================
         * Ambil record hari ini & riwayat
         * =======================================================
         */
        $todayRecord = Absence::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // 🟢 PERBAIKAN B: WAJIB EAGER LOAD RELASI UNTUK FITUR LEMBUR
        // Anda membutuhkan relasi ke 'user' dan 'overtime' (melalui user) 
        // agar Accessor di Model Absensi dapat menghitung 'overtime_pay_formatted'
        $history = Absence::where('user_id', $user->id)
            ->with(['user.overtime']) // <--- PENTING: Tambahkan ini
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('absence.index', compact('todayRecord', 'history'));
    }


    /**
     * =======================================================
     * Check-in user
     * =======================================================
     */
    public function checkIn()
    {
        // Kode Check-In tidak diubah (sudah benar)
        $user = auth()->user();
        $today = now('Asia/Jakarta')->toDateString();
        $currentTime = now('Asia/Jakarta')->format('H:i:s');

        $dailyStatus = DailyStatus::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($dailyStatus && in_array(strtolower($dailyStatus->status), ['izin', 'sakit', 'alpha'])) {
            return back()->with('error', 'Anda tidak bisa check-in karena status hari ini: ' . ucfirst($dailyStatus->status));
        }

        $record = Absence::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($record && $record->time_in) {
            return back()->with('error', 'Sudah absen masuk hari ini.');
        }

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
     * Check-out user
     * =======================================================
     */
    public function checkOut()
{
    $user = auth()->user();
    $today = now('Asia/Jakarta')->toDateString();
    $currentTime = now('Asia/Jakarta')->format('H:i:s'); 

    $overtimeHours = 0.00; // Didefinisikan sebagai float/decimal
    $overtimePay = 0.00;   // Didefinisikan sebagai float/decimal

    // Logika CEGAH CHECK-OUT (Sudah benar)
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

    // 1. Ambil data nominal lembur
    // Memuat relasi 'overtime'. Pastikan relasi ini didefinisikan di Model App\Models\User.
    $user->load('overtime');
    $overtimeRateRecord = $user->overtime;

    // 2. Lakukan perhitungan lembur jika rate tersedia
    // 🟢 PERBAIKAN: Gunakan $overtimeRateRecord bukan $overtimeRate.
    if ($overtimeRateRecord && $overtimeRateRecord->rate_per_hour > 0) {
        
        $endOfWork = Carbon::parse($today . ' 17:00:00', 'Asia/Jakarta');
        $checkoutTime = Carbon::parse($today . ' ' . $currentTime, 'Asia/Jakarta');

        if ($checkoutTime->gt($endOfWork)) {
            
            // Hitung selisih jam lembur (dalam pecahan)
            $overtimeHours = $checkoutTime->floatDiffInHours($endOfWork); 
            
            // Lakukan perhitungan nominal
            $overtimePay = $overtimeHours * $overtimeRateRecord->rate_per_hour;
            
            // Opsional: Pembulatan jam lembur yang dicatat di DB
            $overtimeHours = round($overtimeHours, 2); 
            $overtimePay = round($overtimePay, 2); // Pembulatan bayaran
        }
    }
    
    // 3. SET DAN SIMPAN properti Model ke database
    $record->time_out = $currentTime;
    $record->overtime_hours = $overtimeHours; 
    $record->overtime_pay = $overtimePay; 
    
    $record->save();
    
    // Output success message
    $successMessage = 'Check-out berhasil!';
    if ($overtimePay > 0) {
        $successMessage .= ' Lembur dihitung sebesar Rp ' . number_format($overtimePay, 0, ',', '.') . '.';
    }

    return back()->with('success', $successMessage);
}

    // ... method summary() (tidak diubah)
    public function summary(Request $request)
    {
        $user = auth()->user();
        $month = $request->input('month', now('Asia/Jakarta')->month);
        $year = $request->input('year', now('Asia/Jakarta')->year);

        // Tambahkan eager loading di sini juga, jika summary menggunakan Accessor
        $records = Absence::where('user_id', $user->id)
            ->with(['user.overtime'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $summary = [
            'tepat_waktu' => $records->where('status', 'Tepat Waktu')->count(),
            'terlambat'   => $records->where('status', 'Terlambat')->count(),
            'izin'        => $records->where('status', 'Izin')->count(),
            'sakit'       => $records->where('status', 'Sakit')->count(),
            'alpha'       => $records->where('status', 'Alpha')->count(),
            // Opsional: Total uang lembur di rekap bulanan
            'total_overtime_pay' => $records->sum('overtime_pay')
        ];

        return view('absence.summary', compact('summary', 'month', 'year'));
    }
}