<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Lgu extends Model
{
    protected $fillable = [
        'name',
        'code',
        'province',
        'region',
        'contact_email',
        'contact_phone',
        'address',
        'latitude',
        'longitude',
        'coverage_radius_km',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'coverage_radius_km' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users assigned to this LGU.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the barangays under this LGU.
     */
    public function barangays(): HasMany
    {
        return $this->hasMany(Barangay::class);
    }

    /**
     * Get reports assigned to this LGU.
     */
    public function assignedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'assigned_lgu_id');
    }

    /**
     * Get announcements created by this LGU.
     */
    public function announcements(): HasMany
    {
        return $this->hasMany(PublicAnnouncement::class);
    }

    /**
     * Find the nearest LGU to given coordinates.
     */
    public static function findNearest(float $latitude, float $longitude): ?self
    {
        return self::selectRaw("
            *,
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) AS distance
        ", [$latitude, $longitude, $latitude])
        ->having('distance', '<=', DB::raw('coverage_radius_km'))
        ->where('is_active', true)
        ->orderBy('distance')
        ->first();
    }

    /**
     * Scope to filter only active LGUs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
