<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'alliance_id',
        'name',
        'description',
        'points_required',
        'image_url',
        'stock',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points_required' => 'integer',
        'stock' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }
}
