<?php

namespace App\Services;

use App\Enums\UserAccountActionType;
use App\Exceptions\UserManagementException;
use App\Models\Merchant;
use App\Models\OrganizationMember;
use App\Models\PointAdjustment;
use App\Models\Role;
use App\Models\TwoFactorRecoveryCode;
use App\Models\User;
use App\Models\UserAccountAction;
use App\Notifications\AccountDeactivatedNotification;
use App\Notifications\AccountRestoredNotification;
use App\Notifications\PointsAdjustedNotification;
use App\Notifications\TwoFactorDisabledByAdminNotification;
use App\Notifications\UserCredentialsNotification;
use App\Notifications\UserWelcomeNotification;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserManagementService
{
    /** Roles que necesitan estar ligados a una alianza (comercio) al crearse. */
    private const ALLIANCE_REQUIRED_ROLES = ['admin_merchant', 'merchant'];

    /** Roles de staff con acceso al CRUD de administración de usuarios. */
    private const STAFF_ROLES = ['superadmin', 'moderador'];

    public function __construct(private readonly UserRepository $users)
    {
    }

    public function createUser(array $data, User $admin): User
    {
        $role = Role::findOrFail($data['role_id']);
        $digits12 = str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
        $codeIdentity = $digits12 . User::calculateEan13CheckDigit($digits12);
        $plainPassword = User::generatePassword();

        $user = $this->users->create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($plainPassword),
            'code_identity' => $codeIdentity,
            'role_id' => $role->id,
        ]);

        $needsAlliance = in_array($role->name, self::ALLIANCE_REQUIRED_ROLES, true)
            || ($role->name === 'member' && ! empty($data['alliance_id']));

        if ($needsAlliance) {
            $linkModel = $role->name === 'admin_merchant' ? Merchant::class : OrganizationMember::class;
            $linkModel::create(['user_id' => $user->id, 'alliance_id' => $data['alliance_id']]);
        }

        $user->notify(new UserWelcomeNotification($plainPassword, $role->name));

        $this->logAction($user, $admin, UserAccountActionType::USER_CREATED, null);

        return $user;
    }

    public function modifyPoints(User $target, User $admin, int $points, string $reason): User
    {
        $this->assertActionAllowed($admin, $target, 'modifyPoints');

        if ($target->trashed()) {
            throw new UserManagementException('No se puede realizar esta acción sobre un usuario dado de baja.', 422);
        }

        $previousBalance = $this->users->pointsBalance($target->id);

        if ($previousBalance + $points < 0) {
            throw new UserManagementException(
                'El ajuste dejaría el saldo de puntos en negativo.',
                422,
                "Saldo actual: {$previousBalance}, ajuste solicitado: {$points}."
            );
        }

        PointAdjustment::create([
            'user_id' => $target->id,
            'admin_user_id' => $admin->id,
            'points' => $points,
            'reason' => $reason,
            'created_at' => now(),
        ]);

        $newBalance = $previousBalance + $points;

        $target->notify(new PointsAdjustedNotification($previousBalance, $points, $newBalance, $reason));

        return $target;
    }

    public function deactivate(User $target, User $admin, string $reason): User
    {
        $this->assertActionAllowed($admin, $target, 'deactivate');

        if ($target->trashed()) {
            throw new UserManagementException('El usuario ya está dado de baja.', 422);
        }

        $target->delete();

        $this->logAction($target, $admin, UserAccountActionType::DEACTIVATED, $reason);
        $target->notify(new AccountDeactivatedNotification($reason));

        return $target;
    }

    public function restore(User $target, User $admin, string $reason): User
    {
        $this->assertActionAllowed($admin, $target, 'restore');

        if (! $target->trashed()) {
            throw new UserManagementException('El usuario no está dado de baja.', 422);
        }

        $target->restore();

        $this->logAction($target, $admin, UserAccountActionType::RESTORED, $reason);
        $target->notify(new AccountRestoredNotification($reason));

        return $target;
    }

    public function resetCredentials(User $target, User $admin): User
    {
        $this->assertActionAllowed($admin, $target, 'resetCredentials');

        if ($target->trashed()) {
            throw new UserManagementException('No se puede realizar esta acción sobre un usuario dado de baja.', 422);
        }

        $plainPassword = User::generatePassword();
        $target->password = Hash::make($plainPassword);
        $target->save();

        $this->logAction($target, $admin, UserAccountActionType::CREDENTIALS_RESET, null);
        $target->notify(new UserCredentialsNotification($plainPassword));

        return $target;
    }

    public function disableTwoFactor(User $target, User $admin): User
    {
        $this->assertActionAllowed($admin, $target, 'disableTwoFactor');

        if ($target->trashed()) {
            throw new UserManagementException('No se puede realizar esta acción sobre un usuario dado de baja.', 422);
        }

        $target->two_factor_status = false;
        $target->google2fa_secret = null;
        $target->save();

        TwoFactorRecoveryCode::where('user_id', $target->id)->delete();

        $this->logAction($target, $admin, UserAccountActionType::TWO_FACTOR_DISABLED, null);
        $target->notify(new TwoFactorDisabledByAdminNotification());

        return $target;
    }

    /**
     * Reglas de quién puede ejecutar qué acción de gestión sobre qué cuenta:
     * - Nadie puede actuar sobre su propia cuenta desde este panel, salvo
     *   ajustarse sus propios puntos (evita que un admin/moderador se dé de
     *   baja o se resetee credenciales a sí mismo).
     * - Un moderador no puede gestionar (ninguna acción, ni siquiera puntos)
     *   la cuenta de otro moderador ni la del superadmin — solo puede
     *   gestionar admin_merchant/merchant/member sin restricción.
     * - Un superadmin no tiene restricciones sobre otras cuentas.
     */
    private function assertActionAllowed(User $admin, User $target, string $action): void
    {
        if ($admin->id === $target->id) {
            if ($action !== 'modifyPoints') {
                throw new UserManagementException('No puedes realizar esta acción sobre tu propia cuenta.', 403);
            }

            return;
        }

        $admin->loadMissing('role');
        $target->loadMissing('role');

        if ($admin->role?->name === 'moderador' && in_array($target->role?->name, self::STAFF_ROLES, true)) {
            throw new UserManagementException('No tienes permisos para gestionar cuentas de moderadores o del superadministrador.', 403);
        }
    }

    private function logAction(User $target, User $admin, UserAccountActionType $type, ?string $reason): void
    {
        UserAccountAction::create([
            'target_user_id' => $target->id,
            'actor_user_id' => $admin->id,
            'action_type' => $type,
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }
}
