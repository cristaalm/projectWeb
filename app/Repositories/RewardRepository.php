<?php

namespace App\Repositories;

use App\Models\Reward;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RewardRepository
{
    /**
     * Columnas por las que el listado de recompensas puede ordenarse — allowlist
     * explícita para no aceptar el nombre de columna crudo del request.
     */
    public const SORTABLE_COLUMNS = [
        'id', 'name', 'points_required', 'stock', 'status', 'is_exclusive', 'expires_at', 'created_at',
    ];

    public function findById(int $id, bool $withTrashed = false): ?Reward
    {
        $query = $withTrashed ? Reward::withTrashed() : Reward::query();

        return $query->find($id);
    }

    /**
     * `alliance_id` en $filters no es un filtro opcional más: el controller lo
     * fuerza a la propia alianza cuando quien pide el listado es admin_merchant
     * (ver RewardService/RewardController) — este método no distingue el rol,
     * solo aplica los filtros que recibe.
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Reward::query()->with('alliance');

        if (! empty($filters['alliance_id'])) {
            $query->where('alliance_id', $filters['alliance_id']);
        }

        if (($filters['status'] ?? null) !== null) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['query'])) {
            $term = '%'.$filters['query'].'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'ilike', $term)
                    ->orWhere('description', 'ilike', $term)
                    ->orWhere('code', 'ilike', $term);
            });
        }

        $sortBy = in_array($filters['key'] ?? null, self::SORTABLE_COLUMNS, true) ? $filters['key'] : 'id';
        $sortDir = ($filters['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($filters['per_page'] ?? 15);
    }
}
