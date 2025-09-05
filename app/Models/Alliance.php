<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\AllianceStatus;

class Alliance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_url',
        'contact_name',
        'contact_email',
        'phone',
        'address',
        'status',
    ];

    protected $casts = [
        'status' => AllianceStatus::class,
    ];

    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }
}
