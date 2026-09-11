<?php

namespace App\Models;

use App\Enums\RewardRedemptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointRedemption extends Model
{
    protected $table = 'point_redemptions';

    protected $primaryKey = 'id';

    protected $keyType = 'integer';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'reward_id',
        'alliance_id',
        'merchant_user_id',
        'points_spent',
        'quantity',
        'status',
    ];

    protected $casts = [
        'status' => RewardRedemptionStatus::class,
        'points_spent' => 'integer',
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }

    public function merchantUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_user_id');
    }
}
