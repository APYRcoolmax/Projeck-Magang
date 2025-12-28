<?php

namespace App\Http\Controllers\Admin; // Pastikan namespace sesuai struktur folder Anda

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Salary;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index()
    {
        $employees = User::where('role', '!=', 'admin')
                            ->with('salary')
                            ->orderBy('name')
                            ->get();

        // Ambil contoh data untuk ditampilkan di form global
        $sampleSalary = Salary::first(); 

        return view('admin.salaries.index', compact('employees', 'sampleSalary'));
    }

    /**
     * COCOK DENGAN ROUTE: Route::post('/salaries/settings', ...)
     * Fungsi: Update Potongan (Global)
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'late_deduction'  => 'required|numeric|min:0|max:100',
            'alpha_deduction' => 'required|numeric|min:0|max:100',
        ]);

        // 1. UPDATE MASSAL ke seluruh data salary yang ada
        Salary::query()->update([
            'late_deduction'  => $request->late_deduction,
            'alpha_deduction' => $request->alpha_deduction,
        ]);

        // 2. (Opsional) Jika ada user baru yg belum punya record salary, buatkan defaultnya
        // agar aturan potongan langsung berlaku.
        $usersWithoutSalary = User::where('role', '!=', 'admin')->doesntHave('salary')->get();
        foreach($usersWithoutSalary as $user) {
            Salary::create([
                'user_id' => $user->id,
                'basic_salary' => 0, // Gaji pokok 0 dulu
                'late_deduction' => $request->late_deduction,
                'alpha_deduction' => $request->alpha_deduction
            ]);
        }

        return back()->with('success', 'Potongan global berhasil diperbarui untuk semua karyawan.');
    }

    /**
     * COCOK DENGAN ROUTE: Route::put('/salaries/{id}', ...)
     * Fungsi: Update Gaji Pokok (Individu)
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'basic_salary' => 'required|integer|min:0',
        ]);

        // Kita update basic_salary saja, jangan timpa late_deduction/alpha_deduction
        // Gunakan updateOrCreate untuk jaga-jaga jika data belum ada
        $salary = Salary::where('user_id', $id)->first();

        if ($salary) {
            $salary->update(['basic_salary' => $request->basic_salary]);
        } else {
            // Jika data baru dibuat di sini, ambil default potongan 0 (nanti diupdate via global)
            Salary::create([
                'user_id' => $id,
                'basic_salary' => $request->basic_salary,
                'late_deduction' => 0,
                'alpha_deduction' => 0
            ]);
        }

        return back()->with('success', 'Gaji pokok karyawan berhasil disimpan.');
    }
    
    // Fungsi payrollReport (sesuai route Anda) biarkan kosong dulu atau isi sesuai kebutuhan
    public function payrollReport() {
        return view('admin.salaries.report'); // Contoh return view
    }
}