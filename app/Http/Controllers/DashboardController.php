<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 
use App\Models\Absence;
use App\Models\DailyStatus;
use App\Models\Salary; // Tambahkan ini
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // --- DATA UNTUK ADMIN (Statistik Keseluruhan Karyawan) ---
        if ($user->role === 'admin') {
            $totalPendingApproval = DailyStatus::where('approval_status', 'pending')->count();
            
            $totalEmployees = User::where('role', '!=', 'admin')->count();
            
            $todayPresent = Absence::where('date', $today)
                ->whereHas('user', function($q) { $q->where('role', '!=', 'admin'); })
                ->whereIn('status', ['present', 'late', 'terlambat'])
                ->count();
                
            $todayAbsent = Absence::where('date', $today)
                ->whereHas('user', function($q) { $q->where('role', '!=', 'admin'); })
                ->whereIn('status', ['alpha', 'izin', 'sakit'])
                ->count();

            // --- TAMBAHAN DATA UNTUK GRAFIK (7 HARI TERAKHIR) ---
            $days = collect(range(6, 0))->map(function($i) {
                return now()->subDays($i)->format('Y-m-d');
            });

            $attendanceData = $days->map(function($date) {
                return Absence::where('date', $date)
                    ->whereIn('status', ['present', 'late', 'terlambat'])
                    ->count();
            });

            $labels = $days->map(function($date) {
                return date('D', strtotime($date)); // Nama hari: Mon, Tue, etc.
            });

            // --- TAMBAHAN DATA UNTUK MODAL POP-UP ---
            $presentEmployees = User::where('role', '!=', 'admin')
                ->whereHas('absences', function($q) use ($today) {
                    $q->where('date', $today)->whereIn('status', ['present', 'late', 'terlambat']);
                })->get(['name'])->map(function($u) {
                    return ['name' => $u->name, 'status' => 'Hadir'];
                });

            $allEmployeesStatus = User::where('role', '!=', 'admin')->get(['id', 'name'])
                ->map(function($u) use ($today) {
                    $check = Absence::where('user_id', $u->id)->where('date', $today)->first();
                    return [
                        'name' => $u->name,
                        'status' => $check ? 'Hadir' : 'Alpha/Izin'
                    ];
                });

            // --- TAMBAHAN: AMBIL ATURAN PERSEN DENDA DARI TABEL SALARIES ---
            // Mengambil satu data sebagai sampel aturan persentase yang berlaku
            $salaryRule = Salary::first();

            return view('dashboard', compact(
                'totalPendingApproval',
                'totalEmployees',
                'todayPresent',
                'todayAbsent',
                'presentEmployees',
                'allEmployeesStatus',
                'attendanceData', 
                'labels',
                'salaryRule' // Data dikirim ke view untuk mengisi ringkasan sistem
            ));
        }

        // --- DATA UNTUK USER / KARYAWAN (TIDAK DIUBAH) ---
        $monthlyAbsences = Absence::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $totalWorkingDays = $monthlyAbsences->whereNotNull('time_in')->count();
        $totalOvertimeHours = $monthlyAbsences->sum('overtime_hours');
        $totalOvertimePay = round($monthlyAbsences->sum('overtime_pay'), 2);
        
        $pendingRequests = DailyStatus::where('user_id', $user->id)
            ->where('approval_status', 'pending')
            ->count();
            
        $todayRecord = Absence::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        return view('dashboard', compact(
            'totalWorkingDays',
            'totalOvertimeHours',
            'totalOvertimePay',
            'pendingRequests',
            'todayRecord'
        ));
    }
}