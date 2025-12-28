<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 
use App\Models\Absence;
use App\Models\DailyStatus;
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
        
        // Statistik tambahan agar dashboard admin informatif
        $totalEmployees = User::where('role', '!=', 'admin')->count();
        
        $todayPresent = Absence::where('date', $today)
            ->whereHas('user', function($q) { $q->where('role', '!=', 'admin'); })
            ->whereIn('status', ['present', 'late', 'terlambat'])
            ->count();
            
        $todayAbsent = Absence::where('date', $today)
            ->whereHas('user', function($q) { $q->where('role', '!=', 'admin'); })
            ->whereIn('status', ['alpha', 'izin', 'sakit'])
            ->count();

        // --- TAMBAHAN DATA UNTUK MODAL POP-UP ---
        
        // 1. Ambil nama karyawan yang HADIR saja
        $presentEmployees = User::where('role', '!=', 'admin')
            ->whereHas('absences', function($q) use ($today) {
                $q->where('date', $today)->whereIn('status', ['present', 'late', 'terlambat']);
            })->get(['name'])->map(function($u) {
                return ['name' => $u->name, 'status' => 'Hadir'];
            });

        // 2. Ambil SEMUA karyawan dan cek statusnya (untuk kartu Izin/Alpha)
        $allEmployeesStatus = User::where('role', '!=', 'admin')->get(['id', 'name'])
            ->map(function($u) use ($today) {
                $check = Absence::where('user_id', $u->id)->where('date', $today)->first();
                return [
                    'name' => $u->name,
                    'status' => $check ? 'Hadir' : 'Alpha/Izin'
                ];
            });

        return view('dashboard', compact(
            'totalPendingApproval',
            'totalEmployees',
            'todayPresent',
            'todayAbsent',
            'presentEmployees',   // Data dikirim ke view
            'allEmployeesStatus'  // Data dikirim ke view
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