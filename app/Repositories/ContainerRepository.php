<?php

namespace App\Repositories;

use App\Models\Container;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContainerRepository
{
    /**
     * Columnas por las que el listado de contenedores puede ordenarse — allowlist
     * explícita para no aceptar el nombre de columna crudo del request.
     */
    public const SORTABLE_COLUMNS = [
        'id', 'name', 'serial_number', 'location', 'status', 'created_at',
    ];

    public function findById(int $id): ?Container
    {
        return Container::find($id);
    }

    public function serialNumberExists(string $serialNumber, ?int $ignoreId = null): bool
    {
        return Container::where('serial_number', $serialNumber)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Container::query();

        if (($filters['status'] ?? null) !== null) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['query'])) {
            $term = '%' . $filters['query'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'ilike', $term)
                    ->orWhere('serial_number', 'ilike', $term)
                    ->orWhere('location', 'ilike', $term);
            });
        }

        $sortBy = in_array($filters['key'] ?? null, self::SORTABLE_COLUMNS, true) ? $filters['key'] : 'id';
        $sortDir = ($filters['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($filters['per_page'] ?? 15);
    }
}
