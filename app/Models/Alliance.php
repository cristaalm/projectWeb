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
        'contact_name',
        'contact_email',
        'phone',
        'address',
        'logo',
        'ext',
        'status',
    ];

    protected $casts = [
        'status' => AllianceStatus::class,
        'logo' => 'boolean',
    ];

    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }
}
