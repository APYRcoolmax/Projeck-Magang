<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\Admin\AbsenceAdminController;
use App\Http\Controllers\Admin\OvertimeController;
use App\Http\Controllers\DailyStatusController; 
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// =========================================================================
// USER/KARYAWAN ROUTES (HANYA MEMBUTUHKAN 'auth')
// =========================================================================
Route::middleware('auth')->group(function () {

    // Profile (Dari Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Absensi User
    Route::get('/absence', [AbsenceController::class, 'index'])->name('absence.index');
    Route::post('/absence/check-in', [AbsenceController::class, 'checkIn'])->name('absence.checkin');
    Route::post('/absence/check-out', [AbsenceController::class, 'checkOut'])->name('absence.checkout');

    // Rekap Absensi User
    Route::get('/absence/summary', [AbsenceController::class, 'summary'])->name('absence.summary');
    
    // Status Harian User (Pengajuan)
    Route::get('/daily-status', [DailyStatusController::class, 'index'])->name('daily_status.index'); 
    Route::get('/daily-status/create', [DailyStatusController::class, 'create'])->name('daily_status.create');
    Route::post('/daily-status/store', [DailyStatusController::class, 'store'])->name('daily_status.store');
});


// =========================================================================
// ADMIN ROUTES (MEMBUTUHKAN 'auth' DAN 'isAdmin')
// =========================================================================
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {

    // -- ABSENSI & LAPORAN --
    Route::get('/absences', [AbsenceAdminController::class, 'index'])->name('absences.index');
    Route::get('/absences/create', [AbsenceAdminController::class, 'create'])->name('absences.create');
    Route::post('/absences/store', [AbsenceAdminController::class, 'store'])->name('absences.store');
    Route::get('/absences/{id}/edit', [AbsenceAdminController::class, 'edit'])->name('absences.edit');
    Route::post('/absences/{id}/update', [AbsenceAdminController::class, 'update'])->name('absences.update');
    Route::put('/absences/{id}/status', [AbsenceAdminController::class, 'updateStatus'])->name('absences.updateStatus');

    // Laporan & Export
    Route::get('/reports', [AbsenceAdminController::class, 'reports'])->name('reports.index');
    Route::get('/reports/export/pdf', [AbsenceAdminController::class, 'exportReportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/excel', [AbsenceAdminController::class, 'exportReportExcel'])->name('reports.export.excel');

    // -- VALIDASI STATUS HARIAN (Perbaikan Sinkronisasi dan Penambahan Route Aksi) --
    // GANTI METHOD: dari userValidation() menjadi dailyStatusIndex()
    Route::get('/daily-status', [AbsenceAdminController::class, 'dailyStatusIndex'])->name('daily_status.index'); 
    
    // TAMBAHAN: Route Persetujuan
    Route::post('/daily-status/{id}/approve', [AbsenceAdminController::class, 'approveDailyStatus'])->name('daily_status.approve');
    
    // TAMBAHAN: Route Penolakan
    Route::post('/daily-status/{id}/reject', [AbsenceAdminController::class, 'rejectDailyStatus'])->name('daily_status.reject');


    // -- PENGATURAN LEMBUR & REKAP --
    Route::get('/overtime', [OvertimeController::class, 'index'])->name('overtime.index');
    Route::post('/overtime/update', [OvertimeController::class, 'update'])->name('overtime.update');
});


// Route Otentikasi Breeze/Fortify
require __DIR__ . '/auth.php';