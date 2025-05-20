<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TopUpStatusLog;
use App\Models\User;

class TopUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'proof',
        'payment_method',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(TopUpStatusLog::class, 'topup_id');
    }

}
