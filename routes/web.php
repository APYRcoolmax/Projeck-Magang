<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\Admin\AbsenceAdminController;
use App\Http\Controllers\Admin\OvertimeController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// =======================
// USER ROUTES
// =======================
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Absensi User
    Route::get('/absence', [AbsenceController::class, 'index'])->name('absence.index');
    Route::post('/absence/check-in', [AbsenceController::class, 'checkIn'])->name('absence.checkin');
    Route::post('/absence/check-out', [AbsenceController::class, 'checkOut'])->name('absence.checkout');

    // Rekap User
    Route::get('/absence/summary', [AbsenceController::class, 'summary'])->name('absence.summary');
});


// =======================
// ADMIN ROUTES
// =======================
Route::middleware(['auth', 'isAdmin'])->group(function () {

    // Dashboard Absensi Admin
    Route::get('/admin/absences', [AbsenceAdminController::class, 'index'])
        ->name('admin.absences.index');

    // Export
    Route::get('/admin/absences/export/pdf', [AbsenceAdminController::class, 'exportPdf'])
        ->name('admin.absences.export.pdf');
    Route::get('/admin/absences/export/excel', [AbsenceAdminController::class, 'exportExcel'])
        ->name('admin.absences.export.excel');

    // Edit Status Absensi
    Route::get('/admin/absences/{id}/edit', [AbsenceAdminController::class, 'edit'])
        ->name('admin.absences.edit');
    Route::post('/admin/absences/{id}/update', [AbsenceAdminController::class, 'update'])
        ->name('admin.absences.update');

    // Tambah Absensi Manual
    Route::get('/admin/absences/create', [AbsenceAdminController::class, 'create'])
        ->name('admin.absences.create');
    Route::post('/admin/absences/store', [AbsenceAdminController::class, 'store'])
        ->name('admin.absences.store');

    // Update status dropdown
    Route::put('/admin/absences/{id}/status', [AbsenceAdminController::class, 'updateStatus'])
        ->name('admin.absences.updateStatus');

    // ================== Laporan ================== //
    Route::get('/admin/reports', [AbsenceAdminController::class, 'reports'])
        ->name('admin.reports.index');

    Route::get('/admin/reports/export/pdf', [AbsenceAdminController::class, 'exportReportPdf'])
        ->name('admin.reports.export.pdf');

    Route::get('/admin/reports/export/excel', [AbsenceAdminController::class, 'exportReportExcel'])
        ->name('admin.reports.export.excel');

    // ================== VALIDASI STATUS USER ================== //
    Route::get('/admin/user/validation', [AbsenceAdminController::class, 'userValidation'])
        ->name('admin.user.validation');

    Route::post('/admin/user/validation/update', [AbsenceAdminController::class, 'updateUserValidation'])
        ->name('admin.user.validation.update');

    // ================== LEMBUR ================== //
    Route::get('/admin/overtime', [OvertimeController::class, 'index'])
        ->name('admin.overtime.index');

    Route::post('/admin/overtime/update', [OvertimeController::class, 'update'])
        ->name('admin.overtime.update');
});


require __DIR__ . '/auth.php';
