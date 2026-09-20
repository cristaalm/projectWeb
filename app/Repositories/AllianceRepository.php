<?php

namespace App\Repositories;

use App\Enums\AllianceStatus;
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

    /**
     * Listado público de comercios activos (app móvil). A diferencia de `paginate()`,
     * las columnas se listan a propósito para no exponer nunca los datos de contacto
     * administrativo (contact_name/contact_email/phone), y la búsqueda solo mira
     * nombre y dirección.
     */
    public function paginateShops(array $filters): LengthAwarePaginator
    {
        $query = Alliance::query()
            ->select(['id', 'type_shop_id', 'name', 'address', 'latitude', 'longitude', 'logo_url', 'has_exclusive_rewards'])
            ->with('typeShop:id,name')
            ->where('status', AllianceStatus::ACTIVE->value);

        if (! empty($filters['type_shop_id'])) {
            $query->where('type_shop_id', $filters['type_shop_id']);
        }

        if (! empty($filters['query'])) {
            $term = '%'.$filters['query'].'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'ilike', $term)
                    ->orWhere('address', 'ilike', $term);
            });
        }

        return $query->orderBy('name')->orderBy('id')->paginate($filters['per_page'] ?? 15);
    }
}
