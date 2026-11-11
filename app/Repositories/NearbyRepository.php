<?php

namespace App\Repositories;

use App\Enums\AllianceStatus;
use App\Enums\RewardStatus;
use App\Models\Reward;
use App\Repositories\Concerns\QueriesByDistance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Consultas de los endpoints "cerca de ti" de la app móvil.
 */
class NearbyRepository
{
    use QueriesByDistance;

    /**
     * Recompensas que el usuario podría canjear hoy (sin mirar sus puntos), con
     * su comercio cargado. Si se pasa `$location`, cada una trae `distance_m`
     * (null si el comercio no tiene coordenadas); si además se pasa `$radiusM`,
     * solo quedan las de comercios dentro de ese radio.
     *
     * @param  array{latitude: float, longitude: float}|null  $location
     */
    public function availableRewards(?int $memberAllianceId, ?array $location, ?float $radiusM = null): Collection
    {
        $query = Reward::query()
            ->select('rewards.*')
            ->join('alliances', 'alliances.id', '=', 'rewards.alliance_id')
            ->with('alliance.typeShop:id,name');

        $this->whereRewardAvailable($query, $memberAllianceId);

        if ($location) {
            $this->selectDistance($query, 'alliances.latitude', 'alliances.longitude', $location['latitude'], $location['longitude']);

            if ($radiusM !== null) {
                $this->whereWithinRadius($query, 'alliances.latitude', 'alliances.longitude', $location['latitude'], $location['longitude'], $radiusM);
            }
        }

        return $query->get();
    }

    /**
     * Filtros de "recompensa disponible para este usuario", sobre una consulta de
     * `rewards` que ya hace join con `alliances`:
     * - aprobada (y no borrada: SoftDeletes ya lo excluye),
     * - sin vencer,
     * - con stock (null = ilimitado; stock es lo que queda),
     * - de un comercio activo,
     * - no exclusiva, o exclusiva de la alianza de la que el usuario es miembro.
     */
    public function whereRewardAvailable(Builder $query, ?int $memberAllianceId): Builder
    {
        return $query
            ->where('rewards.status', RewardStatus::APPROVED->value)
            ->where(fn ($q) => $q->whereNull('rewards.expires_at')->orWhere('rewards.expires_at', '>', now()))
            ->where(fn ($q) => $q->whereNull('rewards.stock')->orWhere('rewards.stock', '>', 0))
            ->where('alliances.status', AllianceStatus::ACTIVE->value)
            ->where(function ($q) use ($memberAllianceId) {
                $q->where('rewards.is_exclusive', false);

                if ($memberAllianceId !== null) {
                    $q->orWhere('rewards.alliance_id', $memberAllianceId);
                }
            });
    }
}
