<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RewardsUser extends Model
{
    use HasFactory;

    protected $table = 'rewards_user';
    protected $primaryKey = 'id';
    protected $keyType = 'integer';
    public $incrementing = true;

    // no hay updated_at, solo created_at
    public $timestamps = false;

    protected $fillable = [
        'reward_id',
        'user_id',
        'redeemed_at',
        'quantity',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
        'quantity' => 'integer',
    ];

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
