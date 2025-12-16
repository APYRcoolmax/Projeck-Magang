<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 

class DashboardController extends Controller
{
    public function index()
    {
        $overtimeRates = User::where('role', 'karyawan')
            ->whereHas('overtime', function ($query) {
                $query->where('rate_per_hour', '>', 0);
            })
            ->with('overtime')
            ->select('id', 'name')
            ->get();
            
        // 🔥 TAMBAHKAN INI UNTUK DEBUGGIN
        // 🔥 JANGAN LUPA HAPUS SETELAH DEBUG SELESAI
        
        return view('dashboard', compact('overtimeRates'));
    }
}