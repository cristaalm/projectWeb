<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avatar extends Model
{
    protected $table = 'avatar';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'preferred_language',
        'selected_tone',
        'socratic_mode',
        'feedback_enabled',
        'voice_model',
        'current_mood',
        'state',
        'updated_at',
    ];

    protected $casts = [
        'socratic_mode' => 'boolean',
        'feedback_enabled' => 'boolean',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
