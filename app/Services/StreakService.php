<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserStreak;
use App\Repositories\StreakRepository;
use Carbon\CarbonInterface;

/**
 * Racha de reciclaje por días calendario consecutivos (zona horaria de la
 * app). `user_streaks.updated_at` es la fecha del último reciclaje válido —
 * solo este servicio escribe esa tabla.
 */
class StreakService
{
    public function __construct(
        private readonly StreakRepository $streaks,
    ) {}

    /**
     * Se llama una vez por cada reciclaje válido, dentro de la transacción
     * que registra el scan (ver ScanService::register()).
     */
    public function registerRecycle(User $user): UserStreak
    {
        $now = now();
        $streak = $this->streaks->findByUser($user->id);

        $current = self::nextStreak($streak->current_streak ?? 0, $streak?->updated_at, $now);

        return $this->streaks->save($user->id, [
            'current_streak' => $current,
            'best_streak' => max($streak->best_streak ?? 0, $current),
            'streak_status' => true,
            'updated_at' => $now,
        ]);
    }

    /**
     * Racha tal como debe mostrarse hoy. Ningún proceso apaga las rachas
     * vencidas en la tabla, así que se resuelve al leer: si el último
     * reciclaje es anterior a ayer, la racha actual ya se perdió.
     */
    public function snapshot(int $userId): array
    {
        $streak = $this->streaks->findByUser($userId);
        $alive = $streak !== null && self::isAlive($streak->updated_at, now());

        return [
            'current_streak' => $alive ? $streak->current_streak : 0,
            'best_streak' => $streak->best_streak ?? 0,
            'streak_status' => $alive,
        ];
    }

    /**
     * Racha resultante de registrar un reciclaje en `$now`: el mismo día no
     * la mueve, el día siguiente la extiende, y cualquier hueco la reinicia.
     */
    public static function nextStreak(int $current, ?CarbonInterface $lastRecycleAt, CarbonInterface $now): int
    {
        if ($lastRecycleAt === null || $current < 1) {
            return 1;
        }

        if ($lastRecycleAt->isSameDay($now)) {
            return $current;
        }

        return $lastRecycleAt->isSameDay($now->copy()->subDay()) ? $current + 1 : 1;
    }

    /** La racha sigue viva si el último reciclaje fue hoy o ayer. */
    public static function isAlive(?CarbonInterface $lastRecycleAt, CarbonInterface $now): bool
    {
        if ($lastRecycleAt === null) {
            return false;
        }

        return $lastRecycleAt->isSameDay($now) || $lastRecycleAt->isSameDay($now->copy()->subDay());
    }
}
