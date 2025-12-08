<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportDownvote extends Model
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
     * Get the report that owns this downvote.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the user who downvoted (null if anonymous).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Toggle downvote for a report.
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
            // Remove downvote
            $existing->delete();
            $report->decrement('downvotes_count');
            return false;
        } else {
            // Add downvote
            self::create([
                'report_id' => $report->id,
                'user_id' => $user?->id,
                'ip_address' => $ipAddress,
                'created_at' => now(),
            ]);
            $report->increment('downvotes_count');
            
            // Auto-hide if threshold reached (5 downvotes)
            if ($report->downvotes_count >= 5) {
                $report->is_hidden = true;
                $report->save();
            }
            
            return true;
        }
    }

    /**
     * Check if user/IP has downvoted a report.
     */
    public static function hasDownvoted(Report $report, ?User $user = null, ?string $ipAddress = null): bool
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
