<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicAnnouncement extends Model
{
    protected $fillable = [
        'lgu_id',
        'created_by',
        'title',
        'content',
        'type',
        'is_active',
        'is_pinned',
        'published_at',
        'expires_at',
        'reactions_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'reactions_count' => 'integer',
    ];

    /**
     * Get the LGU that owns this announcement.
     */
    public function lgu(): BelongsTo
    {
        return $this->belongsTo(Lgu::class);
    }

    /**
     * Get the user who created this announcement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active announcements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    /**
     * Scope for pinned announcements.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope for published announcements.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Check if announcement is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
