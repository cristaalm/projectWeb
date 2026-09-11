<?php

namespace App\Repositories;

use App\Models\PointRedemption;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PointRedemptionRepository
{
    /**
     * Columnas por las que el listado de canjes puede ordenarse — allowlist
     * explícita para no aceptar el nombre de columna crudo del request.
     */
    public const SORTABLE_COLUMNS = [
        'id', 'points_spent', 'quantity', 'status', 'created_at',
    ];

    public function findById(int $id): ?PointRedemption
    {
        return PointRedemption::find($id);
    }

    /**
     * `alliance_id` en $filters se fuerza a la propia alianza cuando quien pide
     * el listado es admin_merchant (ver RedemptionService/RedemptionController).
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = PointRedemption::query()->with(['user', 'reward', 'alliance', 'merchantUser']);

        if (! empty($filters['alliance_id'])) {
            $query->where('alliance_id', $filters['alliance_id']);
        }

        if (($filters['status'] ?? null) !== null) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['reward_id'])) {
            $query->where('reward_id', $filters['reward_id']);
        }

        if (! empty($filters['query'])) {
            $term = '%'.$filters['query'].'%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('user', function ($sub) use ($term) {
                    $sub->where('name', 'ilike', $term)
                        ->orWhere('last_name', 'ilike', $term)
                        ->orWhere('email', 'ilike', $term);
                })->orWhereHas('reward', function ($sub) use ($term) {
                    $sub->where('name', 'ilike', $term)
                        ->orWhere('code', 'ilike', $term);
                });
            });
        }

        $sortBy = in_array($filters['key'] ?? null, self::SORTABLE_COLUMNS, true) ? $filters['key'] : 'id';
        $sortDir = ($filters['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($filters['per_page'] ?? 15);
    }
}
