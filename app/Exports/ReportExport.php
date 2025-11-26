<?php

namespace App\Exports;

use App\Models\Absence;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class ReportExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Absence::with('user');

        // Filter Bulanan
        if ($this->request->filled('month') && $this->request->filled('year')) {
            $query->whereMonth('date', $this->request->month)
                  ->whereYear('date', $this->request->year);
        }

        // Filter Harian
        if ($this->request->filled('date')) {
            $query->whereDate('date', $this->request->date);
        }

        return $query->orderBy('date', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'Nama'      => $item->user->name,
                    'Tanggal'   => $item->date,
                    'Check-In'  => $item->time_in ?? '-',
                    'Check-Out' => $item->time_out ?? '-',
                    'Status'    => $item->status,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Tanggal',
            'Check-In',
            'Check-Out',
            'Status'
        ];
    }
}
