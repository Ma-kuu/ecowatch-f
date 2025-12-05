<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportUpvote extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'user_id',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the report that owns this upvote.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the user who upvoted (null if anonymous).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Toggle upvote for a report.
     */
    public static function toggle(Report $report, ?User $user = null, ?string $ipAddress = null): bool
    {
        $query = self::where('report_id', $report->id);

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('ip_address', $ipAddress);
        }

        $existing = $query->first();

        if ($existing) {
            // Remove upvote
            $existing->delete();
            $report->decrement('upvotes_count');
            return false;
        } else {
            // Add upvote
            self::create([
                'report_id' => $report->id,
                'user_id' => $user?->id,
                'ip_address' => $ipAddress,
                'created_at' => now(),
            ]);
            $report->increment('upvotes_count');
            return true;
        }
    }

    /**
     * Check if user/IP has upvoted a report.
     */
    public static function hasUpvoted(Report $report, ?User $user = null, ?string $ipAddress = null): bool
    {
        $query = self::where('report_id', $report->id);

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('ip_address', $ipAddress);
        }

        return $query->exists();
    }
}
