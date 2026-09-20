<?php

namespace App\Repositories;

use App\Enums\PointMovementType;
use App\Enums\RewardRedemptionStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PointRepository
{
    /**
     * Última vez que se movió el saldo del usuario: la fecha más reciente entre
     * los mismos movimientos que componen `UserRepository::pointsBalance()`.
     */
    public function lastChangedAt(int $userId): ?Carbon
    {
        $latest = DB::query()
            ->fromSub($this->movementsUnion($userId), 'm')
            ->max('created_at');

        return $latest ? Carbon::parse($latest) : null;
    }

    /**
     * Movimientos del saldo del usuario, más recientes primero. Cada fila trae
     * `type` (PointMovementType), `source_id`, `points` (con signo), `description`
     * y `created_at`. Paginado en SQL: las 4 fuentes viven en tablas distintas y
     * se unen en una sola consulta.
     */
    public function movements(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return DB::query()
            ->fromSub($this->movementsUnion($userId), 'm')
            ->orderByRaw('m.created_at desc nulls last')
            ->orderBy('m.type')
            ->orderByDesc('m.source_id')
            ->paginate($perPage);
    }

    /**
     * Puntos ganados por actividad en el mes de `$month`: reciclaje (point_earnings)
     * más insignias reclamadas (badge_earnings). No incluye ajustes de admin ni canjes.
     */
    public function earnedInMonth(int $userId, Carbon $month): int
    {
        $range = [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()];

        $earnings = DB::table('point_earnings')
            ->where('user_id', $userId)
            ->whereBetween('created_at', $range)
            ->sum('points');

        $badgeEarnings = DB::table('badge_earnings')
            ->where('user_id', $userId)
            ->whereBetween('created_at', $range)
            ->sum('points');

        return (int) $earnings + (int) $badgeEarnings;
    }

    /**
     * Las 4 fuentes de movimiento, con la misma forma de columnas para poder unirlas.
     * Los canjes solo cuentan REDEEMED/DELIVERED (igual que `pointsBalance()`), para
     * que el historial siempre cuadre con el saldo. Los joins de descripción son LEFT
     * para que un movimiento nunca desaparezca del historial mientras sí suma al saldo.
     * Los tipos van como literales (valores constantes del enum, no input del usuario):
     * un parámetro sin tipo en el SELECT de un UNION no lo puede resolver Postgres.
     */
    private function movementsUnion(int $userId): Builder
    {
        $earning = PointMovementType::EARNING->value;
        $badge = PointMovementType::BADGE->value;
        $adjustment = PointMovementType::ADJUSTMENT->value;
        $redemption = PointMovementType::REDEMPTION->value;

        $earnings = DB::table('point_earnings as pe')
            ->leftJoin('scans as s', 's.id', '=', 'pe.scan_id')
            ->leftJoin('material_types as mt', 'mt.id', '=', 's.material_type_id')
            ->where('pe.user_id', $userId)
            ->selectRaw("'{$earning}'::text as type, pe.id as source_id, pe.points as points, mt.name::text as description, pe.created_at as created_at");

        $badges = DB::table('badge_earnings as be')
            ->leftJoin('badge_user as bu', 'bu.id', '=', 'be.badge_user_id')
            ->leftJoin('badge as b', 'b.id', '=', 'bu.badge_id')
            ->where('be.user_id', $userId)
            ->selectRaw("'{$badge}'::text as type, be.id as source_id, be.points as points, b.name::text as description, be.created_at as created_at");

        $adjustments = DB::table('point_adjustments as pa')
            ->where('pa.user_id', $userId)
            ->selectRaw("'{$adjustment}'::text as type, pa.id as source_id, pa.points as points, pa.reason::text as description, pa.created_at as created_at");

        $redemptions = DB::table('point_redemptions as pr')
            ->leftJoin('rewards as r', 'r.id', '=', 'pr.reward_id')
            ->where('pr.user_id', $userId)
            ->whereIn('pr.status', [RewardRedemptionStatus::REDEEMED->value, RewardRedemptionStatus::DELIVERED->value])
            ->selectRaw("'{$redemption}'::text as type, pr.id as source_id, (-1 * pr.points_spent * pr.quantity) as points, r.name::text as description, pr.created_at as created_at");

        return $earnings->unionAll($badges)->unionAll($adjustments)->unionAll($redemptions);
    }
}
