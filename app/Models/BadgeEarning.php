<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeEarning extends Model
{
    const UPDATED_AT = null;

    protected $table = 'badge_earnings';

    protected $fillable = [
        'user_id',
        'badge_user_id',
        'points',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function badgeUser(): BelongsTo
    {
        return $this->belongsTo(BadgeUser::class);
    }
}
