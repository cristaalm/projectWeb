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
        'total_points',
        'logo',
        'type_shop_id',
        'ext',
        'status',
    ];

    protected $casts = [
        'status' => AllianceStatus::class,
        'logo' => 'boolean',
        'total_points' => 'integer',
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
