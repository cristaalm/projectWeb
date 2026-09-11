<?php

namespace App\Repositories;

use App\Enums\ScanStatus;
use App\Models\AgentFeedback;
use App\Models\AgentMemory;
use App\Models\Avatar;
use App\Models\Badge;
use App\Models\BadgeProgress;
use App\Models\Scan;
use App\Models\UserStreak;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AvatarRepository
{
    /**
     * `avatar` no se crea automáticamente al registrar un usuario, pero el
     * contrato de Evi exige devolver `prefs` siempre — se crea con defaults
     * neutros en el primer acceso.
     */
    public function findOrCreateAvatar(int $userId): Avatar
    {
        return Avatar::firstOrCreate(
            ['user_id' => $userId],
            [
                'preferred_language' => 'es',
                'selected_tone' => 'amigable',
                'socratic_mode' => false,
                'feedback_enabled' => true,
                'voice_model' => 'default',
                'current_mood' => 'neutral',
                'state' => 'idle',
                'updated_at' => now(),
            ]
        );
    }

    public function findStreakByUser(int $userId): ?UserStreak
    {
        return UserStreak::where('user_id', $userId)->first();
    }

    public function countValidScans(int $userId): int
    {
        return Scan::where('user_id', $userId)
            ->where('scan_status', ScanStatus::SUCCESS)
            ->count();
    }

    public function pointsEarnedThisMonth(int $userId): int
    {
        return (int) DB::table('point_earnings')
            ->where('user_id', $userId)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('points');
    }

    /** @return Collection<int, Badge> */
    public function activeBadgesOrdered(): Collection
    {
        return Badge::where('status', true)
            ->orderBy('recycles_required')
            ->get();
    }

    /** @return Collection<int, BadgeProgress> keyed by badge_id */
    public function currentMonthProgressByBadge(int $userId): Collection
    {
        $now = Carbon::now();

        return BadgeProgress::where('user_id', $userId)
            ->whereYear('month', $now->year)
            ->whereMonth('month', $now->month)
            ->get()
            ->keyBy('badge_id');
    }

    /** @return Collection<int, AgentMemory> */
    public function recentMemories(int $userId, int $limit = 3): Collection
    {
        return AgentMemory::where('user_id', $userId)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    public function createMemory(int $userId, string $content, ?array $metadata): AgentMemory
    {
        return AgentMemory::create([
            'user_id' => $userId,
            'content' => $content,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    public function createFeedback(int $userId, string $threadId, int $rating, ?string $comment): AgentFeedback
    {
        return AgentFeedback::create([
            'user_id' => $userId,
            'thread_id' => $threadId,
            'rating' => $rating,
            'comment' => $comment,
            'created_at' => now(),
        ]);
    }

    public function updateAvatarState(Avatar $avatar, ?string $mood, ?string $state): Avatar
    {
        $avatar->fill(array_filter([
            'current_mood' => $mood,
            'state' => $state,
        ], fn ($value) => $value !== null));

        $avatar->updated_at = now();
        $avatar->save();

        return $avatar;
    }
}
