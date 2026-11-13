<?php

namespace App\Models;

use App\Support\BadgeIcons;
use App\Support\BadgeTiers;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * Lo necesario para pintar el emblema igual en web y app móvil: el SVG del
     * icono (cae al predeterminado si `icon` es null) y el nivel visual con sus
     * colores, calculado a partir de `recycles_required`.
     */
    protected $appends = ['icon_url', 'tier'];

    protected function iconUrl(): Attribute
    {
        return Attribute::get(fn () => BadgeIcons::url($this->icon));
    }

    protected function tier(): Attribute
    {
        return Attribute::get(fn () => $this->recycles_required === null
            ? null
            : BadgeTiers::forRecycles($this->recycles_required));
    }

    public function progress(): HasMany
    {
        return $this->hasMany(BadgeProgress::class);
    }

    public function awards(): HasMany
    {
        return $this->hasMany(BadgeUser::class);
    }
}
