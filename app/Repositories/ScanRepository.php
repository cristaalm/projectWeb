<?php

namespace App\Repositories;

use App\Models\Scan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ScanRepository
{
    /**
     * Columnas por las que el listado de escaneos puede ordenarse — allowlist
     * explícita para no aceptar el nombre de columna crudo del request.
     */
    public const SORTABLE_COLUMNS = [
        'id', 'points_awarded', 'scan_status', 'scanned_at', 'created_at',
    ];

    public function findByEventId(string $eventId): ?Scan
    {
        return Scan::where('event_id', $eventId)->first();
    }

    public function create(array $attributes): Scan
    {
        return Scan::create($attributes);
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        // withTrashed: un escaneo sigue siendo consultable aunque su usuario
        // se haya dado de baja después.
        $query = Scan::query()->with([
            'user' => fn ($q) => $q->withTrashed(),
            'container',
            'materialType',
        ]);

        if (($filters['status'] ?? null) !== null) {
            $query->where('scan_status', $filters['status']);
        }

        if (! empty($filters['container_id'])) {
            $query->where('container_id', $filters['container_id']);
        }

        if (! empty($filters['material_type_id'])) {
            $query->where('material_type_id', $filters['material_type_id']);
        }

        if (! empty($filters['query'])) {
            $term = '%'.$filters['query'].'%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('user', function ($sub) use ($term) {
                    $sub->withTrashed()->where(function ($user) use ($term) {
                        $user->where('name', 'ilike', $term)
                            ->orWhere('last_name', 'ilike', $term)
                            ->orWhere('email', 'ilike', $term)
                            ->orWhere('code_identity', 'ilike', $term);
                    });
                })->orWhereHas('container', function ($sub) use ($term) {
                    $sub->where('name', 'ilike', $term)
                        ->orWhere('serial_number', 'ilike', $term);
                });
            });
        }

        $sortBy = in_array($filters['key'] ?? null, self::SORTABLE_COLUMNS, true) ? $filters['key'] : 'id';
        $sortDir = ($filters['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($filters['per_page'] ?? 15);
    }
}
