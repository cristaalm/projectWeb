<?php

namespace App\Repositories;

use App\Models\Alliance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AllianceRepository
{
    /**
     * Columnas por las que el listado de alianzas puede ordenarse — allowlist
     * explícita para no aceptar el nombre de columna crudo del request.
     */
    public const SORTABLE_COLUMNS = [
        'id', 'name', 'contact_name', 'contact_email', 'phone', 'type_shop_id', 'status', 'created_at',
    ];

    public function findById(int $id): ?Alliance
    {
        return Alliance::find($id);
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Alliance::query()->with('typeShop');

        if (($filters['status'] ?? null) !== null) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['type_shop_id'])) {
            $query->where('type_shop_id', $filters['type_shop_id']);
        }

        if (! empty($filters['query'])) {
            $term = '%' . $filters['query'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'ilike', $term)
                    ->orWhere('contact_name', 'ilike', $term)
                    ->orWhere('contact_email', 'ilike', $term)
                    ->orWhere('phone', 'ilike', $term)
                    ->orWhere('address', 'ilike', $term);
            });
        }

        $sortBy = in_array($filters['key'] ?? null, self::SORTABLE_COLUMNS, true) ? $filters['key'] : 'id';
        $sortDir = ($filters['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($filters['per_page'] ?? 15);
    }
}
