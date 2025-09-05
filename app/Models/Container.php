<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\ContainerStatus;

class Container extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'location',
        'latitude',
        'longitude',
        'status',
        'last_maintenance',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'status' => ContainerStatus::class,
        'last_maintenance' => 'datetime',
    ];

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }
}
