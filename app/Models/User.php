<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// --- PERBAIKAN: Tambahkan import ini untuk tipe data relasi ---
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    // --- RELASI MODEL ---

    /**
     * Relasi ke model Absence (Satu User memiliki banyak data Absensi)
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * Relasi ke model Salary (Satu User memiliki satu Gaji Pokok)
     */
    public function salary(): HasOne
    {
        return $this->hasOne(Salary::class);
    }

    /**
     * Relasi ke model Overtime (Satu User memiliki satu pengaturan lembur)
     */
    public function overtime(): HasOne
    {
        return $this->hasOne(Overtime::class, 'user_id'); 
    }
}