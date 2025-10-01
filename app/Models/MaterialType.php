<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialType extends Model
{
    use HasFactory;
    
    protected $table = 'material_types';

    protected $fillable = [
        'name',
        'slug',
        'points',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points' => 'integer',
    ];

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }
}
