<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail

{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'no_hp',     
        'role',    
        'password',
        'avatar',
        'google_id',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'no_hp_verified_at' => 'datetime',
            'password' => 'hashed',
            'allowed_regions' => 'array',
        ];
    }
        public function hasVerifiedPhone()
    {
        return !is_null($this->no_hp_verified_at);
    }

    public function markPhoneAsVerified()
    {
        return $this->forceFill([
            'no_hp_verified_at' => now(),
        ])->save();
    }

}
