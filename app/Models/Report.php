<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report_id',
        'user_id',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'anonymous_token',
        'is_anonymous',
        'violation_type_id',
        'title',
        'description',
        'location_address',
        'barangay_id',
        'purok',
        'sitio',
        'latitude',
        'longitude',
        'status',
        'assigned_lgu_id',
        'assigned_at',
        'lgu_confirmed',
        'user_confirmed',
        'admin_override',
        'resolved_at',
        'is_public',
        'priority',
        'upvotes_count',
        'views_count',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'assigned_at' => 'datetime',
        'lgu_confirmed' => 'boolean',
        'user_confirmed' => 'boolean',
        'admin_override' => 'boolean',
        'resolved_at' => 'datetime',
        'is_public' => 'boolean',
        'upvotes_count' => 'integer',
        'views_count' => 'integer',
    ];

    /**
     * Get the user who reported this (null if anonymous).
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the violation type.
     */
    public function violationType(): BelongsTo
    {
        return $this->belongsTo(ViolationType::class);
    }

    /**
     * Get the barangay.
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * Get the assigned LGU.
     */
    public function assignedLgu(): BelongsTo
    {
        return $this->belongsTo(Lgu::class, 'assigned_lgu_id');
    }

    /**
     * Get the photos for this report.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * Get the notifications for this report.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the validity record for this report.
     */
    public function validity(): HasOne
    {
        return $this->hasOne(ReportValidity::class);
    }

    /**
     * Get the updates/timeline for this report.
     */
    public function updates(): HasMany
    {
        return $this->hasMany(ReportUpdate::class);
    }

    /**
     * Get the upvotes for this report.
     */
    public function upvotes(): HasMany
    {
        return $this->hasMany(ReportUpvote::class);
    }

    /**
     * Scope for public reports.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter reports for a specific LGU.
     */
    public function scopeForLgu($query, int $lguId)
    {
        return $query->where('assigned_lgu_id', $lguId);
    }

    /**
     * Scope to find reports within radius of coordinates.
     */
    public function scopeWithinRadius($query, float $latitude, float $longitude, float $radiusKm)
    {
        return $query->whereRaw("
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) <= ?
        ", [$latitude, $longitude, $latitude, $radiusKm]);
    }

    /**
     * Check if report is fully resolved.
     */
    public function getIsResolvedAttribute(): bool
    {
        return $this->status === 'resolved' &&
               ($this->lgu_confirmed && $this->user_confirmed) || $this->admin_override;
    }

    /**
     * Check if report can be marked as resolved.
     */
    public function getCanBeResolvedAttribute(): bool
    {
        return $this->lgu_confirmed && $this->user_confirmed;
    }

    /**
     * Auto-assign report to nearest LGU.
     */
    public function autoAssign(): bool
    {
        if (!$this->latitude || !$this->longitude) {
            return false;
        }

        $nearestLgu = Lgu::findNearest($this->latitude, $this->longitude);

        if ($nearestLgu) {
            $this->assigned_lgu_id = $nearestLgu->id;
            $this->assigned_at = now();
            $this->save();

            // Create update entry
            $this->updates()->create([
                'created_by' => 1, // System user
                'update_type' => 'assignment',
                'title' => 'Report Auto-Assigned',
                'description' => "Automatically assigned to {$nearestLgu->name}",
            ]);

            return true;
        }

        return false;
    }
}
