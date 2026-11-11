<?php

namespace App\Services;

use App\Models\Reward;
use App\Models\User;
use App\Repositories\NearbyRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;

/**
 * Lógica de los endpoints "cerca de ti" de la app móvil.
 * Decisiones en docs/context/ENDPOINTS_CERCANOS.md (secciones 5 y 6).
 */
class NearbyService
{
    public function __construct(
        private readonly NearbyRepository $nearby,
        private readonly UserRepository $users,
    ) {}

    /**
     * Hasta `$limit` recompensas para el usuario:
     * 1. Las exclusivas de su alianza (si es miembro) encabezan la lista.
     * 2. Dentro de cada grupo, primero las que puede canjear: por distancia si se
     *    usó la ubicación; si no, de mayor a menor puntos (las de más valor que le
     *    alcanzan). Después, las que menos puntos le faltan.
     * 3. Como mucho `nearby.rewards_per_shop` por comercio; si con ese tope no se
     *    llena el límite, se completa con más de los mismos comercios.
     *
     * Con ubicación válida solo cuentan los comercios dentro del radio; si no hay
     * ninguno con recompensas disponibles, se ignora la ubicación (location_used
     * = false) y se responde solo por puntos.
     *
     * @param  array{latitude: float, longitude: float}|null  $location
     * @return array{location_used: bool, total_points: int, rewards: Collection<int, Reward>}
     */
    public function rewards(User $user, ?array $location, int $limit): array
    {
        $balance = $this->users->pointsBalance($user->id);
        $memberAllianceId = $user->organizationMember?->alliance_id;

        $locationUsed = false;
        $candidates = collect();

        if ($location) {
            $candidates = $this->nearby->availableRewards($memberAllianceId, $location, config('nearby.radius_m'));
            $locationUsed = $candidates->isNotEmpty();
        }

        if (! $locationUsed) {
            // Se sigue mandando la ubicación (si la hay) solo para informar distance_m.
            $candidates = $this->nearby->availableRewards($memberAllianceId, $location);
        }

        foreach ($candidates as $reward) {
            // PostgreSQL devuelve el double como string.
            $reward->distance_m = $reward->distance_m !== null ? (float) $reward->distance_m : null;
            $reward->can_redeem = $balance >= $reward->points_required;
            $reward->points_missing = max(0, $reward->points_required - $balance);
        }

        $compare = fn (Reward $a, Reward $b) => $this->compareRewards($a, $b, $locationUsed);

        return [
            'location_used' => $locationUsed,
            'total_points' => $balance,
            'rewards' => $this->capPerShop($candidates->sort($compare)->values(), $limit)->sort($compare)->values(),
        ];
    }

    /**
     * Hasta `$limit` comercios activos a `nearby.radius_m` o menos que tienen al
     * menos una recompensa disponible para el usuario (un comercio con solo
     * exclusivas le sale solo a sus miembros), del más cercano al más lejano.
     *
     * @param  array{latitude: float, longitude: float}  $location
     * @return array{total_points: int, shops: \Illuminate\Database\Eloquent\Collection}
     */
    public function shops(User $user, array $location, int $limit): array
    {
        $balance = $this->users->pointsBalance($user->id);

        return [
            'total_points' => $balance,
            'shops' => $this->nearby->nearbyShops(
                $user->organizationMember?->alliance_id,
                $location,
                config('nearby.radius_m'),
                $balance,
                $limit,
            ),
        ];
    }

    /**
     * Hasta `$limit` contenedores activos a `nearby.radius_m` o menos, del más
     * cercano al más lejano, excluyendo los que tienen todos sus compartimentos
     * llenos (`nearby.container_full_level`). Los que aún no reportan llenado
     * salen con la lista de compartimentos vacía.
     *
     * @param  array{latitude: float, longitude: float}  $location
     */
    public function containers(array $location, int $limit): \Illuminate\Database\Eloquent\Collection
    {
        return $this->nearby->nearbyContainers(
            $location,
            config('nearby.radius_m'),
            config('nearby.container_full_level'),
            $limit,
        );
    }

    private function compareRewards(Reward $a, Reward $b, bool $byDistance): int
    {
        // Las exclusivas solo llegan aquí si son de la alianza del usuario.
        return [! $a->is_exclusive, ! $a->can_redeem] <=> [! $b->is_exclusive, ! $b->can_redeem]
            ?: ($a->can_redeem
                ? ($byDistance ? $a->distance_m <=> $b->distance_m : $b->points_required <=> $a->points_required)
                : ($a->points_missing <=> $b->points_missing ?: ($byDistance ? $a->distance_m <=> $b->distance_m : 0)))
            ?: $a->id <=> $b->id;
    }

    /**
     * Toma, en el orden recibido, hasta `nearby.rewards_per_shop` por comercio;
     * si así no se llega a `$limit`, completa con las que se saltaron, en el
     * mismo orden.
     */
    private function capPerShop(Collection $sorted, int $limit): Collection
    {
        $perShop = config('nearby.rewards_per_shop');
        $taken = collect();
        $skipped = collect();
        $countByShop = [];

        foreach ($sorted as $reward) {
            if ($taken->count() >= $limit) {
                break;
            }

            $shopId = $reward->alliance_id;
            if (($countByShop[$shopId] ?? 0) < $perShop) {
                $taken->push($reward);
                $countByShop[$shopId] = ($countByShop[$shopId] ?? 0) + 1;
            } else {
                $skipped->push($reward);
            }
        }

        // Las que no se alcanzaron a revisar también pueden completar la lista.
        $skipped = $skipped->concat($sorted->slice($taken->count() + $skipped->count()));

        return $taken->concat($skipped->take(max(0, $limit - $taken->count())));
    }
}
