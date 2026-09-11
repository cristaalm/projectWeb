<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BadgeUser extends Model
{
    const UPDATED_AT = null;

    protected $table = 'badge_user';

    protected $fillable = [
        'user_id',
        'badge_id',
        'month',
        'awarded_at',
    ];

    protected $casts = [
        'month' => 'date',
        'awarded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function earning(): HasOne
    {
        return $this->hasOne(BadgeEarning::class);
    }
}
