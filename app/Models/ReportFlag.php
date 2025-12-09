<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportFlag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'user_id',
        'reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the report that was flagged.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the user who flagged (null if guest).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
