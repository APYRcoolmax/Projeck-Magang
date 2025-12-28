<?php

namespace App\Exports;

use App\Models\Absence;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;
    protected $startDate;
    protected $endDate;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->startDate = $request->input('start_date');
        $this->endDate = $request->input('end_date');
    }

    public function collection()
    {
        // Ambil data absensi beserta relasi user dan gaji
        // Tambahkan filter untuk mengecualikan user dengan role 'admin'
        $query = Absence::with(['user.salary'])
            ->whereHas('user', function ($q) {
                $q->where('role', '!=', 'admin');
            });
    
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('date', [$this->startDate, $this->endDate]);
        }
    
        $absences = $query->orderBy('date', 'asc')->get();
        
        return $absences->map(function ($item) {
            $salary = $item->user->salary;
            $basicSalary = $salary->basic_salary ?? 0;
            
            // Ambil nilai persen potongan dari database
            $latePercent = $salary->late_deduction ?? 0;
            $alphaPercent = $salary->alpha_deduction ?? 0;
    
            // Hitung nominal potongan berdasarkan persentase dari Gaji Pokok
            $lateFee = ($latePercent / 100) * $basicSalary;
            $alphaFee = ($alphaPercent / 100) * $basicSalary;
    
            // Logika Penentuan Potongan per baris
            $deduction = 0;
            $statusLower = strtolower($item->status);
            
            if ($statusLower === 'late' || $statusLower === 'terlambat') {
                $deduction = $lateFee;
            } elseif ($statusLower === 'alpha') {
                $deduction = $alphaFee;
            }
            
            return [
                'Nama'         => $item->user->name,
                'Tanggal'      => \Carbon\Carbon::parse($item->date)->format('d/m/Y'),
                'Check-In'     => $item->time_in ?? '-',
                'Check-Out'    => $item->time_out ?? '-',
                'Gaji Pokok'   => number_format($basicSalary, 0, ',', '.'),
                'Lembur (IDR)' => number_format($item->overtime_pay, 0, ',', '.'),
                'Potongan (Rp)'=> number_format($deduction, 0, ',', '.'),
                'Status'       => ucfirst($item->status),
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
            'Gaji Pokok (Rp)',
            'Lembur (Rp)',
            'Potongan Terlambat (Rp)', // Judul kolom potongan
            'Status'
        ];
    }
}