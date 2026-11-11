<?php

namespace App\Repositories;

use App\Enums\AllianceStatus;
use App\Enums\ContainerStatus;
use App\Enums\RewardStatus;
use App\Models\Alliance;
use App\Models\Container;
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
            ->where('alliances.status', AllianceStatus::ACTIVE->value)
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
     * Comercios activos a `$radiusM` metros o menos que tienen al menos una
     * recompensa disponible para el usuario, del más cercano al más lejano.
     * Cada uno trae `distance_m`, `available_rewards_count` y
     * `redeemable_rewards_count` (las que le alcanzan con `$balance`).
     *
     * @param  array{latitude: float, longitude: float}  $location
     */
    public function nearbyShops(?int $memberAllianceId, array $location, float $radiusM, int $balance, int $limit): Collection
    {
        $available = fn (Builder $q) => $this->whereRewardAvailable($q, $memberAllianceId);

        // Columnas listadas a propósito, igual que AllianceRepository::paginateShops():
        // nunca exponer los datos de contacto administrativo.
        $query = Alliance::query()
            ->select(['alliances.id', 'alliances.type_shop_id', 'alliances.name', 'alliances.address', 'alliances.latitude', 'alliances.longitude', 'alliances.logo_url', 'alliances.has_exclusive_rewards'])
            ->with('typeShop:id,name')
            ->where('alliances.status', AllianceStatus::ACTIVE->value)
            ->whereHas('rewards', $available)
            ->withCount([
                'rewards as available_rewards_count' => $available,
                'rewards as redeemable_rewards_count' => fn (Builder $q) => $available($q)->where('rewards.points_required', '<=', $balance),
            ]);

        $this->selectDistance($query, 'alliances.latitude', 'alliances.longitude', $location['latitude'], $location['longitude']);
        $this->whereWithinRadius($query, 'alliances.latitude', 'alliances.longitude', $location['latitude'], $location['longitude'], $radiusM);

        return $query->orderBy('distance_m')->orderBy('alliances.id')->limit($limit)->get();
    }

    /**
     * Contenedores activos a `$radiusM` metros o menos, del más cercano al más
     * lejano, con sus compartimentos (`sensors.materialType`) y `distance_m`.
     * Queda fuera el contenedor cuyos compartimentos están todos a
     * `$fullLevel` % o más; uno sin lecturas todavía sí sale.
     *
     * @param  array{latitude: float, longitude: float}  $location
     */
    public function nearbyContainers(array $location, float $radiusM, float $fullLevel, int $limit): Collection
    {
        // Columnas listadas a propósito: ni serial_number ni el token salen a la app.
        $query = Container::query()
            ->select(['containers.id', 'containers.name', 'containers.location', 'containers.latitude', 'containers.longitude'])
            ->with(['sensors' => fn ($q) => $q->orderBy('sensor_key'), 'sensors.materialType:id,name,slug'])
            ->where('containers.status', ContainerStatus::ACTIVE->value)
            ->where(fn ($q) => $q
                ->whereDoesntHave('sensors')
                ->orWhereHas('sensors', fn ($s) => $s->where('fill_level', '<', $fullLevel)));

        $this->selectDistance($query, 'containers.latitude', 'containers.longitude', $location['latitude'], $location['longitude']);
        $this->whereWithinRadius($query, 'containers.latitude', 'containers.longitude', $location['latitude'], $location['longitude'], $radiusM);

        return $query->orderBy('distance_m')->orderBy('containers.id')->limit($limit)->get();
    }

    /**
     * Filtros de "recompensa disponible para este usuario", solo sobre columnas
     * de `rewards` (sirve también dentro de whereHas/withCount). El estado del
     * comercio (activo) lo filtra quien consulta.
     * - aprobada (y no borrada: SoftDeletes ya lo excluye),
     * - sin vencer,
     * - con stock (null = ilimitado; stock es lo que queda),
     * - no exclusiva, o exclusiva de la alianza de la que el usuario es miembro.
     */
    public function whereRewardAvailable(Builder $query, ?int $memberAllianceId): Builder
    {
        return $query
            ->where('rewards.status', RewardStatus::APPROVED->value)
            ->where(fn ($q) => $q->whereNull('rewards.expires_at')->orWhere('rewards.expires_at', '>', now()))
            ->where(fn ($q) => $q->whereNull('rewards.stock')->orWhere('rewards.stock', '>', 0))
            ->where(function ($q) use ($memberAllianceId) {
                $q->where('rewards.is_exclusive', false);

                if ($memberAllianceId !== null) {
                    $q->orWhere('rewards.alliance_id', $memberAllianceId);
                }
            });
    }
}
