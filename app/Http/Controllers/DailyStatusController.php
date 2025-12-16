<?php

namespace App\Http\Controllers; // Pastikan namespace ini benar!

use Illuminate\Http\Request;
use App\Models\DailyStatus;
use App\Models\User; 
use Illuminate\Validation\Rule;

class DailyStatusController extends Controller // Pastikan nama class ini benar!
{
    // Tampilkan riwayat pengajuan status karyawan
    public function index()
    {
        $history = DailyStatus::where('user_id', auth()->id())
                              ->orderBy('date', 'desc')
                              ->paginate(10);
        
        return view('daily_status.index', compact('history'));
    }
    
    // Tampilkan form pengajuan
    public function create()
    {
        return view('daily_status.create');
    }

    // Simpan pengajuan
    public function store(Request $request)
    {
        $request->validate([
            'date' => [
                'required', 
                'date', 
                Rule::unique('daily_statuses')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
                'after_or_equal:' . now()->toDateString() 
            ],
            'type' => 'required|in:izin,sakit,cuti',
            'reason' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', 
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('daily_status_attachments', 'public');
        }

        DailyStatus::create([
            'user_id' => auth()->id(),
            'date' => $request->date,
            'type' => $request->type,
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
            'approval_status' => 'pending', 
        ]);

        return redirect()->route('daily_status.index')->with('success', 'Pengajuan berhasil dikirim, menunggu persetujuan Admin.');
    }
}