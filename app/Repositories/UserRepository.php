<?php

namespace App\Repositories;

use App\Enums\RewardRedemptionStatus;
use App\Models\PointAdjustment;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAccountAction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    /**
     * Columnas por las que el listado de usuarios puede ordenarse — allowlist
     * explícita para no aceptar el nombre de columna crudo del request (evita
     * inyección SQL vía `sort_by` y ordenar por columnas inexistentes).
     */
    public const SORTABLE_COLUMNS = [
        'id', 'name', 'last_name', 'email', 'phone', 'created_at', 'points_balance', 'role',
    ];

    public function findByEmail(string $email): ?User
    {
        return User::withTrashed()->where('email', $email)->first();
    }

    public function findById(int $id, bool $withTrashed = false): ?User
    {
        $query = $withTrashed ? User::withTrashed() : User::query();

        return $query->find($id);
    }

    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    public function phoneExists(string $phone): bool
    {
        return User::where('phone', $phone)->exists();
    }

    public function codeIdentityExists(string $codeIdentity): bool
    {
        return User::withTrashed()->where('code_identity', $codeIdentity)->exists();
    }

    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    public function update(User $user, array $attributes): User
    {
        $user->update($attributes);

        return $user;
    }

    public function defaultRegistrationRole(): ?Role
    {
        return Role::firstWhere('name', 'member');
    }

    /**
     * Saldo de puntos de un usuario: point_earnings + point_adjustments -
     * redenciones (canjes REDEEMED/DELIVERED). No es una columna real.
     */
    public function pointsBalance(int $userId): int
    {
        $earnings = (int) DB::table('point_earnings')->where('user_id', $userId)->sum('points');
        $adjustments = (int) DB::table('point_adjustments')->where('user_id', $userId)->sum('points');
        $redeemed = (int) DB::table('point_redemptions')
            ->where('user_id', $userId)
            ->whereIn('status', [RewardRedemptionStatus::REDEEMED->value, RewardRedemptionStatus::DELIVERED->value])
            ->selectRaw('COALESCE(SUM(points_spent * quantity), 0) as total')
            ->value('total');

        return $earnings + $adjustments - $redeemed;
    }

    /**
     * Usuario con las relaciones necesarias para el modal de detalle del CRUD
     * de administración: rol, alianza (si aplica) y saldo de puntos calculado.
     */
    public function findDetailed(int $id, bool $withTrashed = false): ?User
    {
        $query = $withTrashed ? User::withTrashed() : User::query();

        $user = $query->with(['role', 'merchant.alliance', 'organizationMember.alliance'])->find($id);

        if (! $user) {
            return null;
        }

        $user->points_balance = $this->pointsBalance($id);

        return $user;
    }

    /**
     * Historial combinado de acciones administrativas sobre una cuenta:
     * user_account_actions (baja/restauración/reset/2FA/creación) y
     * point_adjustments (cambios de puntos), unificados y ordenados por fecha
     * descendente. Sin paginar a propósito — un usuario individual no
     * acumula un volumen que lo justifique.
     */
    public function history(int $userId): Collection
    {
        $accountActions = UserAccountAction::where('target_user_id', $userId)
            ->with('actorUser')
            ->get();

        $pointAdjustments = PointAdjustment::where('user_id', $userId)
            ->with('admin')
            ->get();

        return $accountActions->concat($pointAdjustments)
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * Listado paginado/filtrable/ordenable de usuarios para el CRUD de administración.
     * "points_balance" no es una columna real — se calcula sumando point_earnings +
     * point_adjustments - point_redemptions (canjes REDEEMED/DELIVERED) por usuario.
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        // MAX(...) es seguro aquí: pe/pa/pr ya están agregados por user_id en sus
        // propias subconsultas, así que cada join aporta a lo más una fila por
        // usuario — MAX() no cambia el valor, pero hace la expresión válida bajo
        // GROUP BY (Postgres exige que toda columna no agrupada sea una agregación).
        $balanceExpr = 'MAX(COALESCE(pe.total, 0)) + MAX(COALESCE(pa.total, 0)) - MAX(COALESCE(pr.total, 0))';

        $query = User::query()
            ->select('users.*')
            ->selectRaw("($balanceExpr) as points_balance")
            ->leftJoinSub(
                DB::table('point_earnings')->selectRaw('user_id, SUM(points) as total')->groupBy('user_id'),
                'pe',
                'pe.user_id',
                '=',
                'users.id'
            )
            ->leftJoinSub(
                DB::table('point_adjustments')->selectRaw('user_id, SUM(points) as total')->groupBy('user_id'),
                'pa',
                'pa.user_id',
                '=',
                'users.id'
            )
            ->leftJoinSub(
                DB::table('point_redemptions')
                    ->selectRaw('user_id, SUM(points_spent * quantity) as total')
                    ->whereIn('status', [RewardRedemptionStatus::REDEEMED->value, RewardRedemptionStatus::DELIVERED->value])
                    ->groupBy('user_id'),
                'pr',
                'pr.user_id',
                '=',
                'users.id'
            )
            ->groupBy('users.id')
            ->with('role');

        if (filter_var($filters['with_trashed'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $query->withTrashed();
        }

        if (! empty($filters['role'])) {
            $query->whereHas('role', fn ($r) => $r->where('name', $filters['role']));
        }

        if (! empty($filters['alliance_id'])) {
            $allianceId = $filters['alliance_id'];
            $query->where(function ($q) use ($allianceId) {
                $q->whereHas('merchant', fn ($m) => $m->where('alliance_id', $allianceId))
                    ->orWhereHas('organizationMember', fn ($o) => $o->where('alliance_id', $allianceId));
            });
        }

        if (($filters['points_min'] ?? null) !== null) {
            $query->havingRaw("($balanceExpr) >= ?", [$filters['points_min']]);
        }

        if (($filters['points_max'] ?? null) !== null) {
            $query->havingRaw("($balanceExpr) <= ?", [$filters['points_max']]);
        }

        if (! empty($filters['query'])) {
            $term = '%' . $filters['query'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('users.name', 'ilike', $term)
                    ->orWhere('users.last_name', 'ilike', $term)
                    ->orWhere('users.email', 'ilike', $term)
                    ->orWhere('users.phone', 'ilike', $term);
            });
        }

        $sortBy = in_array($filters['key'] ?? null, self::SORTABLE_COLUMNS, true) ? $filters['key'] : 'id';
        $sortDir = ($filters['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'role') {
            // MAX() por la misma razón que en $balanceExpr: el join a roles es 1:1
            // (users.role_id), pero al estar agrupado por users.id, Postgres exige
            // que cualquier columna de otra tabla en el ORDER BY sea una agregación.
            $query->leftJoin('roles', 'roles.id', '=', 'users.role_id')
                ->orderByRaw("MAX(roles.name) {$sortDir}");
        } elseif ($sortBy === 'points_balance') {
            $query->orderBy('points_balance', $sortDir);
        } else {
            $query->orderBy("users.{$sortBy}", $sortDir);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }
}
