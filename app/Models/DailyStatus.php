<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'type', // Izin, Sakit, Cuti
        'reason',
        'attachment',
        'approval_status', // pending, approved, declined
        'approved_by',
    ];

    // Hubungan (Relationship) dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}