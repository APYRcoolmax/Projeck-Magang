<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    use HasFactory;

    // Pastikan nama tabelnya 'overtimes' sesuai screenshot phpMyAdmin
    protected $table = 'overtimes'; 

    protected $fillable = [
        'user_id',
        'rate_per_hour', // Kolom nominal lembur
        // 'created_at',
        // 'updated_at',
    ];

    /**
     * Relasi balik ke Model User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}