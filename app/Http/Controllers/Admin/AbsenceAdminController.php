<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\User;
use App\Models\DailyStatus;
use Carbon\Carbon;
use App\Models\Overtime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsencesExport;
use App\Exports\ReportExport;

class AbsenceAdminController extends Controller
{
    // ... method index, edit, update, create, store, dailyStatusIndex, approveDailyStatus, rejectDailyStatus (TIDAK DIUBAH) ...

    public function index(Request $request)
{
    $query = Absence::with('user')->whereHas('user', function ($q) {
        $q->where('role', '!=', 'admin');
    });

    // Filter berdasarkan tanggal jika diinput
    if ($request->filled('date')) {
        $query->whereDate('date', $request->date);
    }

    // Filter berdasarkan nama karyawan jika diinput
    if ($request->filled('name')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->name . '%');
        });
    }

    // Urutkan berdasarkan tanggal terbaru dan gunakan pagination
    $absences = $query->orderBy('date', 'desc')->paginate(10);

    return view('admin.absence.index', compact('absences'));
}

    // =========================================================================
    // 🟢 FITUR PERSETUJUAN STATUS HARIAN (PENGALIRAN KARYAWAN)
    // =========================================================================

    /**
     * Tampilkan Daftar Pengajuan Status Harian yang Menunggu Persetujuan (Pending)
     */
    public function dailyStatusIndex()
    {
        $pendingRequests = DailyStatus::with('user')
            ->where('approval_status', 'pending')
            ->orderBy('date', 'asc')
            ->get();
            
        return view('admin.daily_status.index', compact('pendingRequests'));
    }

    /**
     * Logika Persetujuan Pengajuan Status Harian (Approve)
     */
    public function approveDailyStatus(Request $request, $id)
    {
        $status = DailyStatus::findOrFail($id);
        
        if ($status->approval_status !== 'pending') {
            return redirect()->back()->with('error', 'Status sudah diproses.');
        }

        DB::beginTransaction();
        try {
            // 1. Update status di tabel daily_statuses
            $status->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
            ]);

            // 2. Catat absensi ke tabel 'absences' agar masuk laporan bulanan
            $existingAbsence = Absence::where('user_id', $status->user_id)
                                        ->whereDate('date', $status->date)
                                        ->first();

            if (!$existingAbsence) {
                Absence::create([
                    'user_id' => $status->user_id,
                    'date' => $status->date,
                    'status' => $status->type, 
                    'time_in' => null, 
                    'time_out' => null, 
                    'overtime_hours' => 0,
                    'overtime_pay' => 0,
                    'notes' => 'Otomatis dari Pengajuan Status: ' . $status->reason,
                ]);
            } else {
                $existingAbsence->update([
                    'status' => $status->type,
                    'notes' => $existingAbsence->notes . ' | Overridden by Status: ' . $status->reason,
                ]);
            }

            DB::commit();
            
            return redirect()->back()->with('success', 'Pengajuan status berhasil disetujui dan dicatat sebagai absensi.');
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("Approval Daily Status Failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyetujui. Silakan cek log aplikasi. Error: ' . $e->getMessage());
        }
    }

    /**
     * Logika Penolakan Pengajuan Status Harian (Reject)
     */
    public function rejectDailyStatus($id)
    {
        $status = DailyStatus::findOrFail($id);
        
        if ($status->approval_status === 'pending') {
            $status->update([
                'approval_status' => 'declined',
                'approved_by' => auth()->id(),
            ]);
            return redirect()->back()->with('success', 'Pengajuan status berhasil ditolak. Catatan absensi tidak dibuat.');
        }

        return redirect()->back()->with('error', 'Status sudah diproses.');
    }
    
    // =========================================================================
    // 📊 FITUR LAPORAN (REPORTING) - DIPERBAIKI UNTUK PAGINASI
    // =========================================================================

    /**
     * Tampilkan halaman laporan dan filter absensi (Disinkronkan dengan view filter Bulan/Tahun).
     */
    public function reports(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $date = $request->date;
    
        $query = \App\Models\User::where('role', '!=', 'admin')
            ->with(['salary', 'absences' => function($q) use ($month, $year, $date) {
                if ($date) {
                    $q->whereDate('date', $date);
                } else {
                    $q->whereMonth('date', $month)->whereYear('date', $year);
                }
            }]);
    
        $users = $query->paginate(15)->withQueryString();
    
        $reportData = $users->getCollection()->map(function($user) {
            $absences = $user->absences;
            $salaryData = $user->salary;
            $basicSalary = $salaryData->basic_salary ?? 0;
            
            $latePercent = ($salaryData->late_deduction ?? 0) / 100;
            $alphaPercent = ($salaryData->alpha_deduction ?? 0) / 100;
    
            $totalOvertimePay = $absences->sum('overtime_pay');
            $attendanceCount = $absences->whereNotNull('time_in')->count();
            
            // 1. Hitung frekuensi keterlambatan
            $lateCount = $absences->filter(function($item) {
                $status = strtolower(trim($item->status));
                return in_array($status, ['late', 'terlambat']);
            })->count();
    
            // 2. Hitung frekuensi alpha
            $alphaCount = $absences->filter(function($item) {
                $status = strtolower(trim($item->status));
                return in_array($status, ['alpha', 'mangkir']);
            })->count();
    
            // 3. TAMBAHKAN: Hitung frekuensi Sakit
            $sickCount = $absences->filter(function($item) {
                $status = strtolower(trim($item->status));
                return in_array($status, ['sakit', 'sick']);
            })->count();
    
            // 4. TAMBAHKAN: Hitung frekuensi Izin
            $leaveCount = $absences->filter(function($item) {
                $status = strtolower(trim($item->status));
                return in_array($status, ['izin', 'leave', 'permission']);
            })->count();
            
            // KALKULASI POTONGAN
            $lateDeductionTotal = $lateCount * $latePercent * $basicSalary;
            $alphaDeductionTotal = $alphaCount * $alphaPercent * $basicSalary;
            
            $totalDeduction = $lateDeductionTotal + $alphaDeductionTotal;
            $takeHomePay = ($basicSalary + $totalOvertimePay) - $totalDeduction;
    
            return [
                'name' => $user->name,
                'attendance_count' => $attendanceCount,
                'late_count' => $lateCount,
                'alpha_count' => $alphaCount,
                'sick_count' => $sickCount,    // Kirim ke Blade
                'leave_count' => $leaveCount,  // Kirim ke Blade
                'basic_salary' => $basicSalary,
                'overtime_pay' => $totalOvertimePay,
                'total_deduction' => $totalDeduction,
                'take_home_pay' => $takeHomePay,
            ];
        });
    
        return view('admin.reports.index', compact('users', 'reportData', 'month', 'year', 'date'));
    }

    /**
     * Export Laporan Absensi ke PDF.
     */
    public function exportReportPdf(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if (!$start_date || !$end_date) {
            return redirect()->back()->with('error', 'Pilih rentang tanggal untuk export PDF.');
        }

        // Tidak di-paginate, karena kita mau semua data dalam rentang tgl di export
        $absences = Absence::with('user')
            ->whereBetween('date', [$start_date, $end_date])
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy('user_id'); // Grouping di sini karena PDF/Excel biasanya format rekap

        $data = [
            'absences' => $absences,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
        
        $pdf = Pdf::loadView('admin.exports.report_pdf', $data);
        
        $filename = 'Laporan_Absensi_' . $start_date . '_to_' . $end_date . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export Laporan Absensi ke Excel.
     */
    public function exportReportExcel(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if (!$start_date || !$end_date) {
            return redirect()->back()->with('error', 'Pilih rentang tanggal untuk export Excel.');
        }
        
        $filename = 'Laporan_Absensi_' . $start_date . '_to_' . $end_date . '.xlsx';

       return Excel::download(new ReportExport($request), $filename);
    }

    public function create()
{
    // Mengasumsikan Anda memiliki view di resources/views/admin/absences/create.blade.php
    $users = User::orderBy('name')->get(); // Ambil daftar pengguna untuk dropdown
    return view('admin.absence.create', compact('users'));
}

/**
 * Simpan data absensi manual yang baru.
 */
public function store(Request $request)
{
    // 1. Validasi Data Input
    $validatedData = $request->validate([
        'user_id' => ['required', 'exists:users,id'],
        'date' => ['required', 'date'],
        'status' => ['required', 'string', 'in:Hadir,Sakit,Izin,Cuti,Tidak Hadir'],
        'time_in' => ['nullable', 'date_format:H:i'],
        'time_out' => ['nullable', 'date_format:H:i', 'after:time_in'],
        'notes' => ['nullable', 'string', 'max:255'],
    ], [
        'time_out.after' => 'Waktu Keluar harus setelah Waktu Masuk.',
        'user_id.required' => 'Karyawan wajib dipilih.',
        // ... (Tambahkan pesan kustom lainnya)
    ]);

    // 2. Cek duplikasi absensi
    $existingAbsence = Absence::where('user_id', $validatedData['user_id'])
                                ->whereDate('date', $validatedData['date'])
                                ->first();
    if ($existingAbsence) {
        return back()->with('error', 'Absensi untuk karyawan ini pada tanggal tersebut sudah ada. Gunakan fitur Edit jika ingin mengubah data.');
    }

    // 3. Ambil Pengaturan Lembur Karyawan
    $overtimeSetting = Overtime::where('user_id', $validatedData['user_id'])->first(); 

    // Asumsi jam kerja standar 8 jam.
    $standardWorkHours = 8; 
    $ratePerHour = $overtimeSetting ? $overtimeSetting->rate_per_hour : 0;

    $timeIn = null;
    $timeOut = null;
    $lateStatus = 'Hadir';
    $overtimeHours = 0;
    $overtimePay = 0;

    // 4. Proses Waktu dan Hitung Keterlambatan
    if ($validatedData['time_in']) {
        $timeIn = Carbon::parse($validatedData['date'] . ' ' . $validatedData['time_in']);
        $standardTimeIn = Carbon::parse($validatedData['date'] . ' 08:00:00', 'Asia/Jakarta');

        if ($timeIn->greaterThan($standardTimeIn)) {
            $lateStatus = 'Terlambat';
        } else {
            $lateStatus = 'Tepat Waktu';
        }
    }

    if ($validatedData['time_out']) {
        $timeOut = Carbon::parse($validatedData['date'] . ' ' . $validatedData['time_out']);
    }

    // 5. Logika Perhitungan Lembur
    if ($timeIn && $timeOut && $ratePerHour > 0) {
        $workDuration = $timeOut->diffInMinutes($timeIn) / 60; // Durasi dalam Jam (float)

        if ($workDuration > $standardWorkHours) {
            $overtimeHours = $workDuration - $standardWorkHours;
            $overtimePay = $overtimeHours * $ratePerHour;

            // Pembulatan sebelum disimpan
            $overtimeHours = round($overtimeHours, 2);
            $overtimePay = round($overtimePay);
        }
    }

    // 6. Proses Penyimpanan Data
    $absence = Absence::create([
        'user_id' => $validatedData['user_id'],
        'date' => $validatedData['date'],
        'status' => $validatedData['status'],
        'time_in' => $timeIn,
        'time_out' => $timeOut,

        // Simpan status detail:
        'detail_status' => ($validatedData['status'] == 'Hadir' && $timeIn) ? $lateStatus : $validatedData['status'],

        'notes' => $validatedData['notes'],
        'is_manual' => true,
        'is_approved' => true, 

        // Kolom Lembur
        'overtime_hours' => $overtimeHours,
        'overtime_pay' => $overtimePay,
    ]);

    // 7. Redirect dan Notifikasi
    return redirect()->route('admin.absences.index')
        ->with('success', 'Absensi manual untuk ' . $absence->user->name . ' pada tanggal ' . $absence->date->format('d M Y') . ' berhasil disimpan, termasuk perhitungan lembur.');
}

/**
 * Tampilkan form untuk mengedit record absensi.
 */
public function edit($id)
{
    $absence = Absence::findOrFail($id);
    $users = User::orderBy('name')->get(); 
    // Mengasumsikan Anda memiliki view di resources/views/admin/absences/edit.blade.php
    return view('admin.absence.edit', compact('absence', 'users'));
}

/**
 * Update data absensi yang telah diedit.
 */
public function update($id, Request $request)
{
    // Logika validasi dan update data (isi nanti)
    /* $absence = Absence::findOrFail($id);
    $absence->update($request->all());
    */
    return redirect()->route('admin.create.blade')->with('success', 'Absensi berhasil diperbarui.');
}
}