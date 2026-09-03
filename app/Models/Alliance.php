<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\AllianceStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alliance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'contact_email',
        'phone',
        'address',
        'latitude',
        'longitude',
        'logo_url',
        'has_exclusive_rewards',
        'type_shop_id',
        'status',
    ];

    protected $casts = [
        'status' => AllianceStatus::class,
        'has_exclusive_rewards' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(History::class);
    }

    public function typeShop(): BelongsTo
    {
        return $this->belongsTo(TypeShop::class);
    }
}
