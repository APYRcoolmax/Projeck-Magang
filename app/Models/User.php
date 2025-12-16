<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Overtime; // <<< TAMBAH: Import Model Overtime

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'referral_code',
        // 'overtime_hourly_rate' // <<< OPSIONAL: Tambahkan ini jika Anda telah membuat kolomnya di database
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel overtimes (Satu User memiliki satu Overtime Rate)
     */
    public function overtime()
    {
        // PENTING: Menggunakan Overtime::class yang sudah di-import di atas
        return $this->hasOne(Overtime::class, 'user_id'); 
    }
}