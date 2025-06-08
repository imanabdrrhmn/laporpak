<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'request_data',
        'api_response',
        'is_paid',
        'name',
        'id_number',
        'phone_number', 
    ];

    protected $casts = [
        'request_data' => 'json',
        'api_response' => 'json',
        'is_paid' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}