<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportUpdate extends Model
{
    protected $fillable = [
        'report_id',
        'created_by',
        'update_type',
        'title',
        'description',
        'progress_percentage',
        'old_value',
        'new_value',
    ];

    protected $casts = [
        'progress_percentage' => 'integer',
    ];

    /**
     * Get the report that owns this update.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the user who created this update.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to filter by update type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('update_type', $type);
    }

    /**
     * Scope for status change updates.
     */
    public function scopeStatusChanges($query)
    {
        return $query->where('update_type', 'status_change');
    }

    /**
     * Scope for progress updates.
     */
    public function scopeProgressUpdates($query)
    {
        return $query->where('update_type', 'progress');
    }
}
