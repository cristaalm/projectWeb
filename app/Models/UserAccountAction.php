<?php

namespace App\Models;

use App\Enums\UserAccountActionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccountAction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'target_user_id',
        'actor_user_id',
        'action_type',
        'reason',
        'created_at',
    ];

    protected $casts = [
        'action_type' => UserAccountActionType::class,
        'created_at' => 'datetime',
    ];

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function actorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
