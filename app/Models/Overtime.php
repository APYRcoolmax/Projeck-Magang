<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $fillable = [
        'user_id',
        'rate_per_hour',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
