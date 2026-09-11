<?php

namespace App\Repositories;

use App\Models\Badge;
use App\Models\BadgeProgress;
use App\Models\BadgeUser;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BadgeRepository
{
    /**
     * Columnas por las que el listado de insignias puede ordenarse — allowlist
     * explícita para no aceptar el nombre de columna crudo del request.
     */
    public const SORTABLE_COLUMNS = [
        'id', 'name', 'recycles_required', 'points_awarded', 'status', 'created_at',
    ];

    public function findById(int $id): ?Badge
    {
        return Badge::find($id);
    }

    public function nameExists(string $name, ?int $ignoreId = null): bool
    {
        return Badge::whereRaw('LOWER(name) = LOWER(?)', [$name])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }

    /**
     * badge_progress/badge_user tienen ON DELETE CASCADE hacia badge — un
     * DELETE nunca lanza una violación de FK que se pueda atrapar, así que
     * esta comprobación explícita es la única defensa contra perder
     * historial de otorgamiento (y los puntos ya acreditados en cascada
     * hacia badge_earnings) al eliminar una insignia.
     */
    public function hasAwardHistory(int $badgeId): bool
    {
        return BadgeUser::where('badge_id', $badgeId)->exists();
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Badge::query();

        if (($filters['status'] ?? null) !== null) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['query'])) {
            $term = '%'.$filters['query'].'%';
            $query->where('name', 'ilike', $term);
        }

        $sortBy = in_array($filters['key'] ?? null, self::SORTABLE_COLUMNS, true) ? $filters['key'] : 'id';
        $sortDir = ($filters['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Catálogo de insignias activas — pensado para el selector de una futura
     * app móvil, columnas mínimas necesarias para mostrar el emblema.
     */
    public function activeCatalog(): Collection
    {
        return Badge::where('status', true)
            ->orderBy('recycles_required')
            ->get(['id', 'name', 'icon', 'recycles_required', 'points_awarded']);
    }

    public function findOrCreateProgress(int $userId, int $badgeId, Carbon $month): BadgeProgress
    {
        return BadgeProgress::firstOrCreate(
            ['user_id' => $userId, 'badge_id' => $badgeId, 'month' => $month->toDateString()],
            ['recycles_count' => 0, 'completed' => false]
        );
    }

    public function progressForMonth(int $userId, Carbon $month): Collection
    {
        return BadgeProgress::where('user_id', $userId)
            ->whereDate('month', $month->toDateString())
            ->get()
            ->keyBy('badge_id');
    }

    public function findProgress(int $userId, int $badgeId, Carbon $month): ?BadgeProgress
    {
        return BadgeProgress::where('user_id', $userId)
            ->where('badge_id', $badgeId)
            ->whereDate('month', $month->toDateString())
            ->first();
    }

    /**
     * Progreso con completed=true (de cualquier mes) sin su fila
     * correspondiente en badge_user — insignias listas para reclamar.
     */
    public function pendingClaims(int $userId): Collection
    {
        return BadgeProgress::with('badge')
            ->where('user_id', $userId)
            ->where('completed', true)
            ->whereNotExists(function ($query) {
                $query->selectRaw(1)
                    ->from('badge_user')
                    ->whereColumn('badge_user.user_id', 'badge_progress.user_id')
                    ->whereColumn('badge_user.badge_id', 'badge_progress.badge_id')
                    ->whereColumn('badge_user.month', 'badge_progress.month');
            })
            ->orderBy('month')
            ->get();
    }

    public function alreadyAwarded(int $userId, int $badgeId, Carbon $month): bool
    {
        return BadgeUser::where('user_id', $userId)
            ->where('badge_id', $badgeId)
            ->whereDate('month', $month->toDateString())
            ->exists();
    }

    public function history(int $userId): Collection
    {
        return BadgeUser::with('badge')
            ->where('user_id', $userId)
            ->orderByDesc('awarded_at')
            ->get();
    }
}
