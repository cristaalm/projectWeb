<?php

namespace App\Services;

use App\Enums\RewardStatus;
use App\Exceptions\RewardException;
use App\Models\Alliance;
use App\Models\Reward;
use App\Models\User;

class RewardService
{
    /** Roles con acceso irrestricto al catálogo completo (cualquier alianza) y a aprobar/rechazar. */
    private const STAFF_ROLES = ['superadmin', 'moderador'];

    /**
     * Fuerza el listado a la propia alianza cuando quien pide el listado no es
     * staff — admin_merchant nunca ve recompensas de otra alianza, sin importar
     * qué mande en el query string.
     */
    public function scopeAllianceFilter(array $filters, User $actor): array
    {
        if ($this->isStaff($actor)) {
            return $filters;
        }

        $alliance = $actor->currentAlliance();

        if (! $alliance) {
            throw new RewardException('Tu cuenta no está enlazada a ninguna alianza.', 422);
        }

        $filters['alliance_id'] = $alliance->id;

        return $filters;
    }

    /**
     * admin_merchant solo puede crear para su propia alianza y queda pendiente
     * de revisión; staff crea para cualquier alianza y se aprueba de una vez
     * (no tiene sentido que un superadmin/moderador se pida revisión a sí mismo).
     */
    public function create(array $data, User $actor): Reward
    {
        $isStaff = $this->isStaff($actor);

        if ($isStaff) {
            $allianceId = $data['alliance_id'];
        } else {
            $alliance = $actor->currentAlliance();

            if (! $alliance) {
                throw new RewardException('Tu cuenta no está enlazada a ninguna alianza.', 422);
            }

            $allianceId = $alliance->id;
        }

        $this->assertExclusiveAllowed($allianceId, $data['is_exclusive'] ?? false);

        $reward = new Reward([
            'alliance_id' => $allianceId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'points_required' => $data['points_required'],
            'stock' => $data['stock'] ?? null,
            'is_exclusive' => $data['is_exclusive'] ?? false,
            'expires_at' => $data['expires_at'] ?? null,
            'code' => $this->generateCode(),
        ]);

        if ($isStaff) {
            $reward->status = RewardStatus::APPROVED;
            $reward->approved_by = $actor->id;
            $reward->approved_at = now();
        } else {
            $reward->status = RewardStatus::PENDING;
        }

        $reward->save();

        return $reward;
    }

    /**
     * Editar el contenido de una recompensa ya aprobada/pausada/rechazada,
     * hecho por su propio admin_merchant, la regresa a PENDING (necesita
     * revisión otra vez) y limpia el resultado de la revisión anterior.
     * Cuando edita staff, el estado no se toca — staff ya es la autoridad.
     */
    public function update(Reward $reward, array $data, User $actor): Reward
    {
        $isStaff = $this->isStaff($actor);
        $this->assertOwnership($actor, $reward, $isStaff);

        $allianceId = ($isStaff && ! empty($data['alliance_id'])) ? $data['alliance_id'] : $reward->alliance_id;

        $this->assertExclusiveAllowed($allianceId, $data['is_exclusive'] ?? false);

        $reward->fill([
            'alliance_id' => $allianceId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'points_required' => $data['points_required'],
            'stock' => $data['stock'] ?? null,
            'is_exclusive' => $data['is_exclusive'] ?? false,
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        if (! $isStaff && $reward->status !== RewardStatus::PENDING) {
            $reward->status = RewardStatus::PENDING;
            $reward->approved_by = null;
            $reward->approved_at = null;
            $reward->rejected_by = null;
            $reward->rejected_at = null;
            $reward->rejection_reason = null;
        }

        $reward->save();

        return $reward;
    }

    public function approve(Reward $reward, User $actor): Reward
    {
        if ($reward->status !== RewardStatus::PENDING) {
            throw new RewardException('Solo se pueden aprobar recompensas pendientes de revisión.', 422);
        }

        $reward->status = RewardStatus::APPROVED;
        $reward->approved_by = $actor->id;
        $reward->approved_at = now();
        $reward->rejected_by = null;
        $reward->rejected_at = null;
        $reward->rejection_reason = null;
        $reward->save();

        return $reward;
    }

    public function reject(Reward $reward, User $actor, string $reason): Reward
    {
        if ($reward->status !== RewardStatus::PENDING) {
            throw new RewardException('Solo se pueden rechazar recompensas pendientes de revisión.', 422);
        }

        $reward->status = RewardStatus::REJECTED;
        $reward->rejected_by = $actor->id;
        $reward->rejected_at = now();
        $reward->rejection_reason = $reason;
        $reward->approved_by = null;
        $reward->approved_at = null;
        $reward->save();

        return $reward;
    }

    public function pause(Reward $reward, User $actor): Reward
    {
        $this->assertOwnership($actor, $reward);

        if ($reward->status !== RewardStatus::APPROVED) {
            throw new RewardException('Solo se pueden pausar recompensas aprobadas.', 422);
        }

        $reward->status = RewardStatus::PAUSED;
        $reward->save();

        return $reward;
    }

    public function reactivate(Reward $reward, User $actor): Reward
    {
        $this->assertOwnership($actor, $reward);

        if ($reward->status !== RewardStatus::PAUSED) {
            throw new RewardException('Solo se pueden reactivar recompensas pausadas.', 422);
        }

        $reward->status = RewardStatus::APPROVED;
        $reward->save();

        return $reward;
    }

    public function delete(Reward $reward, User $actor): void
    {
        $this->assertOwnership($actor, $reward);

        $reward->delete();
    }

    private function assertExclusiveAllowed(int $allianceId, bool $isExclusive): void
    {
        if (! $isExclusive) {
            return;
        }

        if (! Alliance::where('id', $allianceId)->value('has_exclusive_rewards')) {
            throw new RewardException('Esta alianza no tiene habilitadas las recompensas exclusivas.', 422);
        }
    }

    private function assertOwnership(User $actor, Reward $reward, ?bool $isStaff = null): void
    {
        if ($isStaff ?? $this->isStaff($actor)) {
            return;
        }

        $alliance = $actor->currentAlliance();

        if (! $alliance || $alliance->id !== $reward->alliance_id) {
            throw new RewardException('No tienes permisos para gestionar esta recompensa.', 403);
        }
    }

    private function isStaff(User $actor): bool
    {
        $actor->loadMissing('role');

        return in_array($actor->role?->name, self::STAFF_ROLES, true);
    }

    private function generateCode(): string
    {
        $digits12 = str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);

        return $digits12.Reward::calculateEan13CheckDigit($digits12);
    }
}
