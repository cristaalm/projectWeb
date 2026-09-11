<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    use HasFactory;

    protected $table = 'badge';

    protected $fillable = [
        'name',
        'icon',
        'recycles_required',
        'points_awarded',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'recycles_required' => 'integer',
        'points_awarded' => 'integer',
    ];

    public function progress(): HasMany
    {
        return $this->hasMany(BadgeProgress::class);
    }

    public function awards(): HasMany
    {
        return $this->hasMany(BadgeUser::class);
    }
}
