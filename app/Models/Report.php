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
        'region',
        'status',
        'reason', 
        'service',
        'source', 
        'address',
        'reason'
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'status' => 'pending',
        'latitude' => 'float',
        'longitude' => 'float',
        'region' => 'string',
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

        public function scopeAllowedRegions($query, $user)
    {
        $allowedRegions = $user->allowed_regions ?? [];

        if (empty($allowedRegions)) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('region', $allowedRegions);
    }
        public function flags()
    {
        return $this->hasMany(ReportFlag::class);
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('admin') || $user->hasRole('verifier'))  {
            return $query->where(function ($subQuery) use ($user) {
                $viewableServices = [];
                if ($user->can('view_reports_penipuan')) {
                    $viewableServices[] = 'Penipuan';
                }
                if ($user->can('view_reports_infrastruktur')) {
                    $viewableServices[] = 'Infrastruktur';
                }

                if (empty($viewableServices)) {
                    return $subQuery->whereRaw('1 = 0');
                }
                $subQuery->whereIn('service', $viewableServices);

                if ($user->can('view_reports_by_region')) {
                    $allowedRegions = $user->allowed_regions ?? [];
                    if (empty($allowedRegions)) {
                        return $subQuery->whereRaw('1 = 0');
                    }
                    $subQuery->whereIn('region', $allowedRegions);
                }
            });
        }

        return $query->whereRaw('1 = 0');
    }

}
