<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Overtime;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'karyawan')->with('overtime')->get();
        return view('admin.overtime.index', compact('users'));
    }

    public function update(Request $request)
    {
        foreach ($request->rate as $userId => $rate) {
            Overtime::updateOrCreate(
                ['user_id' => $userId],
                ['rate_per_hour' => $rate]
            );
        }

        return back()->with('success', 'Nominal lembur berhasil diperbarui!');
    }
}
