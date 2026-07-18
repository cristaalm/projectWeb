<?php

namespace App\Services\Auth;

use App\Enums\AllianceStatus;
use App\Enums\UserStatus;
use App\Exceptions\Auth\AuthException;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\PersonalAccessToken;
use PragmaRX\Google2FA\Google2FA;

class AuthService
{
    /** Roles allowed to establish a web (cookie/session) dashboard login. */
    private const WEB_ROLES = ['superadmin', 'moderador', 'admin_merchant'];

    /** Roles that must be linked to an alliance (via merchant/organizationMember) to log in. */
    private const ALLIANCE_LINKED_ROLES = ['admin_merchant', 'merchant'];

    public function __construct(private readonly UserRepository $users)
    {
    }

    /**
     * Validates credentials + account/role/alliance state. Shared by both the
     * session (web) and token (mobile) login paths.
     */
    public function attemptCredentials(string $email, string $password): User
    {
        $user = $this->users->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new AuthException('Correo electrónico o contraseña incorrectos.', 401);
        }

        if ($user->status !== UserStatus::ACTIVE) {
            throw new AuthException('Tu cuenta ha sido desactivada por un administrador.', 403);
        }

        $user->load('role');

        if (! $user->role || ! $user->role->is_active) {
            throw new AuthException('Tu cuenta no tiene permiso para acceder al sistema.', 403);
        }

        if (in_array($user->role->name, self::ALLIANCE_LINKED_ROLES, true)) {
            $user->load(['merchant', 'organizationMember']);
            $alliance = $user->currentAlliance();

            if (! $alliance) {
                throw new AuthException(
                    'Tu cuenta no es válida, contacta con el administrador.',
                    403,
                    'El usuario comerciante no tiene un comercio asignado.'
                );
            }

            if ($alliance->status !== AllianceStatus::ACTIVE) {
                throw new AuthException(
                    'El comercio al que perteneces ya no está vigente.',
                    403,
                    'El comercio al que perteneces está desactivado.'
                );
            }
        }

        return $user;
    }

    /**
     * Web dashboard logins are restricted to staff roles — mobile/token logins
     * are open to any active role (member, merchant, etc).
     */
    public function assertRoleAllowedForSession(User $user): void
    {
        if (! in_array($user->role->name, self::WEB_ROLES, true)) {
            throw new AuthException('No tienes permiso para acceder al sistema.', 403);
        }
    }

    public function loginSession(Request $request, User $user): User
    {
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return $user;
    }

    /**
     * @return array{0: string, 1: \Illuminate\Support\Carbon}
     */
    public function loginToken(User $user, bool $rememberMe): array
    {
        $minutes = $rememberMe
            ? config('tokens.remember_expiration_minutes')
            : config('tokens.default_expiration_minutes');

        $expiresAt = Carbon::now()->addMinutes($minutes);
        $token = $user->createToken('auth-token', expiresAt: $expiresAt);

        return [$token->plainTextToken, $expiresAt];
    }

    public function register(array $data): User
    {
        if ($this->users->emailExists($data['email'])) {
            throw new AuthException('El correo electrónico ya está en uso.', 422);
        }

        if ($this->users->phoneExists($data['phone'])) {
            throw new AuthException('El número de teléfono ya está en uso.', 422);
        }

        $digits12 = str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
        $checkDigit = User::calculateEan13CheckDigit($digits12);
        $role = $this->users->defaultRegistrationRole();

        return $this->users->create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'code_identity' => $digits12 . $checkDigit,
            'role_id' => $role?->id,
            'google2fa_secret' => (new Google2FA())->generateSecretKey(),
        ]);
    }

    public function forgotPassword(string $email): void
    {
        if (! $this->users->findByEmail($email)) {
            throw new AuthException('El correo electrónico no está registrado en el sistema.', 404);
        }

        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new AuthException(__($status), 422);
        }
    }

    public function resetPassword(array $data): void
    {
        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->password = Hash::make($password);
                $user->save();

                $user->notify(new ResetPasswordNotification());
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new AuthException(__($status), 422);
        }
    }

    /**
     * @return array{user: User, two_factor_pending: bool}
     */
    public function validateSession(Request $request): array
    {
        $user = $request->user();
        $user->load('role');

        if ($user->status !== UserStatus::ACTIVE) {
            throw new AuthException('Tu cuenta no está activa.', 403);
        }

        return [
            'user' => $user,
            'two_factor_pending' => $this->twoFactorPending($request),
        ];
    }

    /**
     * Single source of truth for "is 2FA still pending this session/token" —
     * storage differs by guard (session flag vs. token ability) but the decision
     * logic lives in exactly one place.
     */
    public function twoFactorPending(Request $request): bool
    {
        $user = $request->user();

        if (! $user->two_factor_status) {
            return false;
        }

        if ($request->hasSession()) {
            return ! session('2fa_verified', false);
        }

        $token = $user->currentAccessToken();

        return ! ($token instanceof PersonalAccessToken && in_array('two_factor', $token->abilities ?? [], true));
    }

    public function markTwoFactorVerified(Request $request): void
    {
        if ($request->hasSession()) {
            $request->session()->put('2fa_verified', true);

            return;
        }

        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->forceFill(['abilities' => ['two_factor']])->save();
        }
    }

    public function clearTwoFactorVerified(Request $request): void
    {
        if ($request->hasSession()) {
            $request->session()->forget('2fa_verified');

            return;
        }

        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->forceFill(['abilities' => ['*']])->save();
        }
    }

    public function logout(Request $request): void
    {
        if ($request->hasSession() && Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return;
        }

        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }
}
