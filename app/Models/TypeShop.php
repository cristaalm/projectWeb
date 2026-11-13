<?php

namespace App\Models;

use App\Support\TypeShopIcons;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeShop extends Model
{
    use HasFactory;

    protected $table = 'type_shop';

    protected $fillable = [
        'name',
        'icon',
        'is_active',
    ];

    /** URL absoluta del SVG del icono — la app móvil puede pintarlo sin librería de iconos. */
    protected $appends = ['icon_url'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected function iconUrl(): Attribute
    {
        return Attribute::get(fn () => TypeShopIcons::url($this->icon));
    }

    /**
     * Forma mínima de la categoría para anidarla en otras respuestas (comercios,
     * usuarios ligados a una alianza). Requiere haber cargado `id,name,icon`.
     *
     * @return array{id: int, name: string, icon: ?string, icon_url: ?string}
     */
    public function toSummary(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'icon_url' => $this->icon_url,
        ];
    }
}
