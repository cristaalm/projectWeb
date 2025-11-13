<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\ContainerStatus;

class Container extends Model
{
    use HasFactory;

    protected $table = 'containers';

    protected $fillable = [
        'name',
        'serial_number',
        'location',
        'capacity',
        'status',
    ];

    protected $casts = [
        'capacity' => 'array',
        'status' => ContainerStatus::class,
    ];

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }
}
