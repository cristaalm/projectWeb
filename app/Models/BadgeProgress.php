<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeProgress extends Model
{
    protected $table = 'badge_progress';

    protected $fillable = [
        'user_id',
        'badge_id',
        'month',
        'recycles_count',
        'completed',
    ];

    protected $casts = [
        'month' => 'date',
        'recycles_count' => 'integer',
        'completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }
}
