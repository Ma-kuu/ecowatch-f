<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportValidity extends Model
{
    protected $table = 'report_validity';

    protected $fillable = [
        'report_id',
        'status',
        'reviewed_by',
        'reviewed_at',
        'notes',
        'is_disputed',
        'disputed_by',
        'disputed_at',
        'dispute_reason',
    ];

    protected $casts = [
        'is_disputed' => 'boolean',
        'reviewed_at' => 'datetime',
        'disputed_at' => 'datetime',
    ];

    /**
     * Get the report that owns this validity record.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the user who reviewed this.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the user who disputed this.
     */
    public function disputer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disputed_by');
    }

    /**
     * Mark as valid.
     */
    public function markAsValid(int $userId, ?string $notes = null): void
    {
        $this->update([
            'status' => 'valid',
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Mark as invalid.
     */
    public function markAsInvalid(int $userId, ?string $notes = null): void
    {
        $this->update([
            'status' => 'invalid',
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Dispute the validity decision.
     */
    public function dispute(int $userId, string $reason): void
    {
        $this->update([
            'is_disputed' => true,
            'disputed_by' => $userId,
            'disputed_at' => now(),
            'dispute_reason' => $reason,
            'status' => 'disputed',
        ]);
    }
}
