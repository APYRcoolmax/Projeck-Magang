<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'basic_salary',
        'late_deduction', 
        'alpha_deduction',
    ];

    /**
     * Relasi ke user yang memiliki gaji ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
