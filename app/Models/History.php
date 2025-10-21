<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class History extends Model
{
    use HasFactory;

    protected $table = 'history';

    protected $fillable = [
        'user_id',
        'comerciant_id',
        'type_history',
        'scan_id',
        'material_type_id',
        'reward_id',
        'points',
        'quantity',
        'alliance_id',
    ];

    protected $casts = [
        'type_history' => 'integer',
        'scan_id' => 'integer',
        'points' => 'integer',
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comerciant(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialTypes::class);
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class)->withTrashed();
    }

    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }
}
