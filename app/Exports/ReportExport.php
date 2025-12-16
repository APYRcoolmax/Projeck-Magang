<?php

namespace App\Exports;

use App\Models\Absence;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Carbon\Carbon; // Digunakan untuk memformat tanggal

class ReportExport implements FromCollection, WithHeadings
{
    protected $request;
    protected $startDate;
    protected $endDate;

    // Constructor sudah benar, menerima objek Request
    public function __construct(Request $request)
    {
        // Menghapus spasi khusus Non-Breaking Space jika ada
        $this->request = $request;
        
        // Ambil start_date dan end_date yang dikirim dari JavaScript di view
        $this->startDate = $request->input('start_date');
        $this->endDate = $request->input('end_date');
    }

    public function collection()
    {
        $query = Absence::with('user');

        // Filter data berdasarkan start_date dan end_date
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('date', [$this->startDate, $this->endDate]);
        }

        $absences = $query->orderBy('date', 'asc')->get();
        
        return $absences->map(function ($item) {
            // Logika untuk menampilkan upah lembur dalam format Rupiah
            $overtimePay = property_exists($item, 'overtime_pay_formatted') && $item->overtime_pay_formatted 
                            ? $item->overtime_pay_formatted 
                            : number_format($item->overtime_pay, 0, ',', '.');
            
            return [
                'Nama'      => $item->user->name,
                'Tanggal'   => Carbon::parse($item->date)->format('d/m/Y'), // Format Tanggal lebih jelas
                'Check-In'  => $item->time_in ?? '-',
                'Check-Out' => $item->time_out ?? '-',
                'Lembur (IDR)' => $overtimePay, 
                'Status'    => ucfirst($item->status),
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
            'Lembur (IDR)',
            'Status'
        ];
    }
}