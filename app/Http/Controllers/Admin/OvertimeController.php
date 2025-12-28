<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Overtime; // Pastikan model Overtime benar
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    /**
     * Menampilkan daftar pengguna (karyawan dan admin) untuk mengatur nominal lembur.
     *
     * @return \Illuminate\View\View
     */
    public function index()
{
    // 🟢 PERBAIKAN: Menghapus 'admin' dari daftar
    // Hanya menyertakan role 'karyawan' atau selain 'admin' agar admin tidak perlu diatur lemburnya.
    $users = User::where('role', '!=', 'admin')
                  ->with('overtime')
                  ->orderBy('name') 
                  ->get();
                  
    return view('admin.overtime.index', compact('users'));
}

    /**
     * Memperbarui nominal lembur per jam untuk pengguna yang dipilih.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Pastikan Anda melakukan validasi data sebelum menyimpan ke database
        $request->validate([
            'rate' => 'required|array',
            'rate.*' => 'nullable|numeric|min:0', // Memastikan setiap nilai adalah angka
        ]);

        foreach ($request->rate as $userId => $rate) {
            
            // Konversi ID pengguna ke integer (untuk keamanan)
            $userId = (int) $userId; 
            
            // Lakukan update atau create record Overtime
            Overtime::updateOrCreate(
                ['user_id' => $userId],
                ['rate_per_hour' => $rate ?? 0] // Pastikan menggunakan 0 jika rate kosong
            );
        }

        return back()->with('success', 'Nominal lembur berhasil diperbarui!');
    }
}