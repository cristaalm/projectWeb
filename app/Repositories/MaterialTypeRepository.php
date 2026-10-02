<?php

namespace App\Repositories;

use App\Models\MaterialTypes;
use Illuminate\Database\Eloquent\Collection;

class MaterialTypeRepository
{
    public function findBySlug(string $slug): ?MaterialTypes
    {
        return MaterialTypes::where('slug', $slug)->first();
    }

    /**
     * Catálogo completo (incluidos los inactivos) para la vista de
     * Materiales y el selector de filtros del listado de escaneos — un
     * material desactivado puede seguir teniendo escaneos históricos que
     * filtrar.
     */
    public function catalog(): Collection
    {
        return MaterialTypes::orderBy('name')->get(['id', 'name', 'slug', 'points', 'is_active']);
    }
}
