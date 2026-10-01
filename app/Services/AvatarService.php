<?php

namespace App\Services;

use App\Exceptions\AvatarException;
use App\Models\AgentFeedback;
use App\Models\AgentMemory;
use App\Models\Avatar;
use App\Models\IdentityVerification;
use App\Repositories\AvatarRepository;
use App\Repositories\ContainerRepository;
use App\Repositories\PointRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Schema;

class AvatarService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly ContainerRepository $containers,
        private readonly AvatarRepository $avatarRepo,
        private readonly PointRepository $points,
        private readonly StreakService $streaks,
        private readonly BadgeService $badges,
    ) {}

    /**
     * Snapshot completo que Evi carga una sola vez al identificarse en el
     * contenedor y cachea en RAM durante toda la sesión (no vuelve a
     * consultar por turno).
     */
    public function identify(string $codeIdentity, string $containerSerialNumber): array
    {
        $container = $this->containers->findBySerialNumber($containerSerialNumber);

        if (! $container) {
            throw new AvatarException('Contenedor no encontrado.', 404);
        }

        $user = $this->users->findByCodeIdentity($codeIdentity);

        if (! $user) {
            throw new AvatarException('Código de identidad no válido.', 404);
        }

        $avatar = $this->avatarRepo->findOrCreateAvatar($user->id);
        [$badge, $nextBadge] = $this->badges->monthlySummary($user->id);
        $verification = $this->latestVerification($user->id);

        return [
            'container' => [
                'id' => $container->id,
                'serial_number' => $container->serial_number,
            ],
            'user_id' => $user->id,
            'code_identity' => $user->code_identity,
            'name' => $user->name,
            'last_name' => $user->last_name,
            'total_points' => $this->users->pointsBalance($user->id),
            'valid_scans' => $this->avatarRepo->countValidScans($user->id),
            'streak' => $this->streaks->snapshot($user->id),
            'prefs' => [
                'tone' => $avatar->selected_tone,
                'socratic_mode' => (bool) $avatar->socratic_mode,
                'lang' => $avatar->preferred_language,
                'voice' => $avatar->voice_model,
                'feedback' => (bool) $avatar->feedback_enabled,
            ],
            'tour' => (bool) $user->tour,
            'points_month' => $this->points->earnedInMonth($user->id, now()),
            'badge' => $badge,
            'next_badge' => $nextBadge,
            'recent_memories' => $this->avatarRepo->recentMemories($user->id)
                ->pluck('content')
                ->values()
                ->all(),
            'alliance_id' => $user->currentAlliance()?->id,
            'role_id' => $user->role_id,
            'verification_status' => $verification?->status?->name,
        ];
    }

    /**
     * `identity_verifications` no tiene migración todavía en este esquema (módulo KYC
     * pendiente de la reescritura de BD) — `verification_status` es opcional en el
     * contrato de Evi, así que se omite en vez de romper el snapshot completo.
     */
    private function latestVerification(int $userId): ?IdentityVerification
    {
        if (! Schema::hasTable('identity_verifications')) {
            return null;
        }

        return IdentityVerification::where('user_id', $userId)->latest()->first();
    }

    public function storeMemory(int $userId, string $content, ?array $metadata): AgentMemory
    {
        return $this->avatarRepo->createMemory($userId, $content, $metadata);
    }

    public function storeFeedback(int $userId, string $threadId, int $rating, ?string $comment): AgentFeedback
    {
        return $this->avatarRepo->createFeedback($userId, $threadId, $rating, $comment);
    }

    public function updateAvatarState(int $userId, ?string $mood, ?string $state): Avatar
    {
        if ($mood === null && $state === null) {
            throw new AvatarException('Debes enviar current_mood o state.', 422);
        }

        $avatar = $this->avatarRepo->findOrCreateAvatar($userId);

        return $this->avatarRepo->updateAvatarState($avatar, $mood, $state);
    }
}
