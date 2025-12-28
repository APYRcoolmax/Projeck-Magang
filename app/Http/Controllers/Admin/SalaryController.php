<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Salary;
use App\Models\Setting;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    /**
     * Menampilkan daftar semua karyawan beserta data gaji & potongan mereka.
     */
    public function index()
    {
        $employees = User::where('role', '!=', 'admin')
                            ->with('salary')
                            ->orderBy('name')
                            ->get();

        // Tetap ambil potongan global sebagai cadangan jika diperlukan
        $lateDeduction = Setting::where('key', 'late_deduction_amount')->first();

        return view('admin.salaries.index', compact('employees', 'lateDeduction'));
    }

    /**
     * Menyimpan atau memperbarui gaji pokok DAN potongan individu karyawan.
     */
    public function update(Request $request, string $id)
    {
        // 1. Validasi semua input baru
        $request->validate([
            'basic_salary'    => 'required|integer|min:0',
            'late_deduction'  => 'nullable|integer|min:0',
            'alpha_deduction' => 'nullable|integer|min:0',
        ]);

        // 2. Update atau buat data di tabel salaries
        Salary::updateOrCreate(
            ['user_id' => $id], 
            [
                'basic_salary'    => $request->basic_salary,
                'late_deduction'  => $request->late_deduction ?? 0,
                'alpha_deduction' => $request->alpha_deduction ?? 0,
            ]
        );

        return redirect()->route('admin.salaries.index')->with('success', 'Data gaji dan potongan karyawan berhasil diperbarui.');
    }

    /**
     * Method Store disamakan logikanya dengan Update
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'         => 'required|exists:users,id',
            'basic_salary'    => 'required|integer|min:0',
            'late_deduction'  => 'nullable|integer|min:0',
            'alpha_deduction' => 'nullable|integer|min:0',
        ]);

        Salary::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'basic_salary'    => $request->basic_salary,
                'late_deduction'  => $request->late_deduction ?? 0,
                'alpha_deduction' => $request->alpha_deduction ?? 0,
            ]
        );

        return redirect()->route('admin.salaries.index')->with('success', 'Gaji karyawan berhasil diperbarui.');
    }

    /**
     * UPDATE SETTINGS GLOBAL (Opsional jika masih ingin digunakan)
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'late_deduction_amount' => 'required|integer|min:0',
        ]);

        Setting::updateOrCreate(
            ['key' => 'late_deduction_amount'],
            ['value' => $request->late_deduction_amount]
        );

        return redirect()->route('admin.salaries.index')->with('success', 'Pengaturan potongan global berhasil diperbarui.');
    }
}