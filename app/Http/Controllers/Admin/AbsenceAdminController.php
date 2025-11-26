<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsencesExport;
use App\Exports\ReportExport;

class AbsenceAdminController extends Controller
{
    /**
     * -------------------------------
     * 1. Halaman Manajemen Absensi
     * -------------------------------
     */
    public function index(Request $request)
    {
        $query = Absence::with('user');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        $absences = $query->orderBy('date', 'desc')->paginate(10);

        return view('admin.absence.index', compact('absences'));
    }


    /**
     * -------------------------------
     * 2. Edit Status Absensi
     * -------------------------------
     */
    public function edit($id)
    {
        $absence = Absence::with('user')->findOrFail($id);
        return view('admin.absence.edit', compact('absence'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $absence = Absence::findOrFail($id);
        $absence->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->route('admin.absences.index')
            ->with('success', 'Status absensi berhasil diperbarui!');
    }


    /**
     * -------------------------------
     * 3. Tambah Absensi Manual
     * -------------------------------
     */
    public function create()
    {
        $users = User::where('role', 'karyawan')->get();
        return view('admin.absence.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'date'     => 'required|date',
            'time_in'  => 'nullable',
            'time_out' => 'nullable',
            'status'   => 'required|string',
            'notes'    => 'nullable|string'
        ]);

        Absence::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->route('admin.absences.index')
            ->with('success', 'Absensi berhasil ditambahkan!');
    }


    /**
     * -------------------------------
     * 4. Laporan Absensi
     * -------------------------------
     */
    public function reports(Request $request)
    {
        $query = Absence::with('user');

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('date', $request->month)
                ->whereYear('date', $request->year);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $absences = $query->orderBy('date', 'desc')->get();

        return view('admin.report.index', compact('absences'));
    }


    /**
     * -------------------------------
     * 5. Export PDF
     * -------------------------------
     */
    public function exportReportPdf(Request $request)
    {
        $query = Absence::with('user');

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('date', $request->month)
                ->whereYear('date', $request->year);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $absences = $query->orderBy('date', 'desc')->get();

        $pdf = Pdf::loadView('admin.report.pdf', compact('absences'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-absensi.pdf');
    }


    /**
     * -------------------------------
     * 6. Export Excel
     * -------------------------------
     */
    public function exportReportExcel(Request $request)
    {
        return Excel::download(
            new ReportExport($request),
            'laporan-absensi.xlsx'
        );
    }
}
