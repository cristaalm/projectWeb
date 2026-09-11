<?php

namespace App\Services;

use App\Exceptions\BadgeException;
use App\Models\Badge;
use App\Models\BadgeEarning;
use App\Models\BadgeUser;
use App\Models\User;
use App\Repositories\BadgeRepository;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    public function __construct(
        private readonly BadgeRepository $badges,
    ) {}

    public function create(array $data): Badge
    {
        if ($this->badges->nameExists($data['name'])) {
            throw new BadgeException('Ya existe una insignia con ese nombre.', 422);
        }

        return Badge::create($data);
    }

    public function update(Badge $badge, array $data): Badge
    {
        if ($this->badges->nameExists($data['name'], $badge->id)) {
            throw new BadgeException('Ya existe una insignia con ese nombre.', 422);
        }

        $badge->update($data);

        return $badge;
    }

    public function delete(Badge $badge): void
    {
        // badge_progress/badge_user son ON DELETE CASCADE hacia badge, así que
        // un DELETE nunca falla por FK — hay que comprobar el historial antes
        // de borrar, o se pierde en cascada (incluidos los puntos ya
        // acreditados en badge_earnings vía badge_user).
        if ($this->badges->hasAwardHistory($badge->id)) {
            throw new BadgeException(
                'No se puede eliminar la insignia porque tiene progreso o historial de usuarios vinculado.',
                422
            );
        }

        $badge->delete();
    }

    public function catalog(): Collection
    {
        return $this->badges->activeCatalog();
    }

    /**
     * Enganche pensado para el futuro flujo de Scan: se llama una vez por
     * cada reciclaje válido registrado (un scan = un reciclaje). Solo avanza
     * el progreso del mes actual — el otorgamiento es un reclamo manual
     * aparte, ver claim().
     */
    public function registerRecycle(User $user): void
    {
        $month = now()->startOfMonth();

        Badge::where('status', true)->get()->each(function (Badge $badge) use ($user, $month) {
            $progress = $this->badges->findOrCreateProgress($user->id, $badge->id, $month);

            if ($progress->completed) {
                return;
            }

            $progress->increment('recycles_count');

            if ($progress->recycles_count >= $badge->recycles_required) {
                $progress->update(['completed' => true]);
            }
        });
    }

    /**
     * Progreso del usuario en el mes actual sobre todas las insignias
     * activas, con recycles_count=0 cuando todavía no existe una fila de
     * progreso (no se fuerza su creación en una simple lectura).
     */
    public function myProgress(User $user): Collection
    {
        $month = now()->startOfMonth();
        $progressByBadge = $this->badges->progressForMonth($user->id, $month);

        return Badge::where('status', true)->get()->map(function (Badge $badge) use ($progressByBadge) {
            $progress = $progressByBadge->get($badge->id);

            return [
                'badge' => $badge,
                'recycles_count' => $progress->recycles_count ?? 0,
                'completed' => $progress->completed ?? false,
            ];
        });
    }

    public function pendingClaims(User $user): Collection
    {
        return $this->badges->pendingClaims($user->id);
    }

    public function myHistory(User $user): Collection
    {
        return $this->badges->history($user->id);
    }

    public function claim(User $user, Badge $badge, Carbon $month): BadgeUser
    {
        $progress = $this->badges->findProgress($user->id, $badge->id, $month);

        if (! $progress || ! $progress->completed) {
            throw new BadgeException('Aún no has completado esta insignia en el mes indicado.', 422);
        }

        if ($this->badges->alreadyAwarded($user->id, $badge->id, $month)) {
            throw new BadgeException('Ya reclamaste esta insignia este mes.', 422);
        }

        try {
            return DB::transaction(function () use ($user, $badge, $month) {
                $badgeUser = BadgeUser::create([
                    'user_id' => $user->id,
                    'badge_id' => $badge->id,
                    'month' => $month->toDateString(),
                    'awarded_at' => now(),
                ]);

                BadgeEarning::create([
                    'user_id' => $user->id,
                    'badge_user_id' => $badgeUser->id,
                    'points' => $badge->points_awarded,
                ]);

                return $badgeUser;
            });
        } catch (QueryException $e) {
            if ($e->getCode() === '23505') {
                throw new BadgeException('Ya reclamaste esta insignia este mes.', 422);
            }
            throw $e;
        }
    }
}
