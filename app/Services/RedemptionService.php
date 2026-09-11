<?php

namespace App\Services;

use App\Enums\RewardRedemptionStatus;
use App\Exceptions\RedemptionException;
use App\Models\PointRedemption;
use App\Models\User;

class RedemptionService
{
    /** Roles con acceso irrestricto a los canjes de cualquier alianza. */
    private const STAFF_ROLES = ['superadmin', 'moderador'];

    /**
     * Fuerza el listado a la propia alianza cuando quien pide el listado no es
     * staff, igual que RewardService::scopeAllianceFilter().
     */
    public function scopeAllianceFilter(array $filters, User $actor): array
    {
        if ($this->isStaff($actor)) {
            return $filters;
        }

        $alliance = $actor->currentAlliance();

        if (! $alliance) {
            throw new RedemptionException('Tu cuenta no está enlazada a ninguna alianza.', 422);
        }

        $filters['alliance_id'] = $alliance->id;

        return $filters;
    }

    /**
     * Confirma la entrega en persona de un canje (Canjeado → Entregado). Cuando
     * lo marca el propio admin_merchant (no staff), queda registrado como quien
     * entregó — merchant_user_id — si todavía no tenía uno asignado.
     */
    public function markDelivered(PointRedemption $redemption, User $actor): PointRedemption
    {
        $isStaff = $this->isStaff($actor);
        $this->assertOwnership($actor, $redemption, $isStaff);

        if ($redemption->status !== RewardRedemptionStatus::REDEEMED) {
            throw new RedemptionException('Solo se pueden marcar como entregados los canjes en estado Canjeado.', 422);
        }

        $redemption->status = RewardRedemptionStatus::DELIVERED;

        if (! $isStaff && ! $redemption->merchant_user_id) {
            $redemption->merchant_user_id = $actor->id;
        }

        $redemption->save();

        return $redemption;
    }

    private function assertOwnership(User $actor, PointRedemption $redemption, ?bool $isStaff = null): void
    {
        if ($isStaff ?? $this->isStaff($actor)) {
            return;
        }

        $alliance = $actor->currentAlliance();

        if (! $alliance || $alliance->id !== $redemption->alliance_id) {
            throw new RedemptionException('No tienes permisos para gestionar este canje.', 403);
        }
    }

    private function isStaff(User $actor): bool
    {
        $actor->loadMissing('role');

        return in_array($actor->role?->name, self::STAFF_ROLES, true);
    }
}
