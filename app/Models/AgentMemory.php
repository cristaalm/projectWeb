<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentMemory extends Model
{
    protected $table = 'agent_memories';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'content',
        'metadata',
        'embedding',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'embedding' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
