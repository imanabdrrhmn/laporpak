<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'category',
        'description',
        'evidence',
        'latitude',
        'longitude',
        'status',
        'service',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * Cast attributes to native types.
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * A report belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
