<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Import Carbon untuk manipulasi waktu

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
        'status',
        // 🟢 TAMBAHAN: Kolom untuk menyimpan data lembur
        'overtime_hours', 
        'overtime_pay',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relasi ke Model User.
     */
    public function user()
    {
        // Pastikan Anda telah mengimpor Model User: use App\Models\User;
        return $this->belongsTo(User::class);
    }

    // =========================================================================
    // 🟢 ACCESSOR: Menghitung dan Memformat Uang Lembur untuk Tampilan
    // =========================================================================

    /**
     * Accessor untuk mendapatkan nilai uang lembur yang diformat Rupiah.
     * Accessor ini dipanggil di Blade View menggunakan $record->overtime_pay_formatted
     */
    public function getOvertimePayFormattedAttribute()
    {
        // Jika kolom 'overtime_pay' sudah disimpan di database (seperti di Controller checkOut)
        if ($this->overtime_pay && $this->overtime_pay > 0) {
            // Langsung format nilai yang sudah disimpan
            return 'Rp ' . number_format($this->overtime_pay, 0, ',', '.');
        }

        // Jika kolom 'overtime_pay' belum disimpan (atau diisi manual oleh Admin), 
        // kita perlu menghitungnya lagi menggunakan data user dan overtime rate.
        // Namun, ini hanya dijalankan jika data relasi user dan overtime di-load!
        
        // Cek apakah relasi user dan rate lembur tersedia
        if (!$this->time_out || !$this->user || !$this->user->overtime || !$this->user->overtime->rate_per_hour) {
            return 'Rp 0';
        }

        try {
            $normal_end = Carbon::parse($this->date->toDateString() . ' 17:00:00'); // Asumsi jam selesai 17:00
            $checkout_time = Carbon::parse($this->date->toDateString() . ' ' . $this->time_out);

            if ($checkout_time->greaterThan($normal_end)) {
                $overtime_duration_hours = $checkout_time->floatDiffInHours($normal_end);
                $rate_per_hour = $this->user->overtime->rate_per_hour;

                $total_pay = $overtime_duration_hours * $rate_per_hour;

                return 'Rp ' . number_format($total_pay, 0, ',', '.');
            }
        } catch (\Exception $e) {
            // Error handling, misalnya format waktu tidak valid
        }

        return 'Rp 0';
    }
}