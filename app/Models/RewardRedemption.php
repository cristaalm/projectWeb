<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\RewardRedemptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RewardRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'redeemed_by',
        'reward_name',
        'reward_image_url',
        'points_used',
        'status',
        'expires_at',
        'redeemed_at',
    ];

    protected $casts = [
        'points_used' => 'integer',
        'status' => RewardRedemptionStatus::class,
        'expires_at' => 'datetime',
        'redeemed_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function redeemedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by');
    }
}
