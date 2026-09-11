<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStreak extends Model
{
    protected $table = 'user_streaks';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'current_streak',
        'best_streak',
        'streak_status',
        'updated_at',
    ];

    protected $casts = [
        'current_streak' => 'integer',
        'best_streak' => 'integer',
        'streak_status' => 'boolean',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
