<?php

namespace App\Repositories;

use App\Models\TypeShop;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class TypeShopRepository
{
    /**
     * Columnas por las que el listado de categorías puede ordenarse — allowlist
     * explícita para no aceptar el nombre de columna crudo del request.
     */
    public const SORTABLE_COLUMNS = ['id', 'name', 'is_active', 'created_at'];

    public function findById(int $id): ?TypeShop
    {
        return TypeShop::find($id);
    }

    public function nameExists(string $name, ?int $ignoreId = null): bool
    {
        return TypeShop::whereRaw('LOWER(name) = ?', [Str::lower($name)])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = TypeShop::query();

        if (($filters['is_active'] ?? null) !== null) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (! empty($filters['query'])) {
            $query->where('name', 'ilike', '%' . $filters['query'] . '%');
        }

        $sortBy = in_array($filters['key'] ?? null, self::SORTABLE_COLUMNS, true) ? $filters['key'] : 'id';
        $sortDir = ($filters['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($filters['per_page'] ?? 15);
    }
}
