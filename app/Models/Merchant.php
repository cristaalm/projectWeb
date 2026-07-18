<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Merchant extends Model
{
    protected $fillable = [
        'user_id',
        'alliance_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }
}
