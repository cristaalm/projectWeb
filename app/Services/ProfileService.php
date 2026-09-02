<?php

namespace App\Services;

use App\Exceptions\ProfileException;
use App\Models\User;
use App\Notifications\EmailChangedNotification;
use App\Notifications\PasswordChangedNotification;
use App\Repositories\UserRepository;
use App\Services\Auth\AuthService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;

class ProfileService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly AuthService $authService,
    ) {
    }

    public function updateProfile(User $user, array $data): User
    {
        return $this->users->update($user, [
            'name' => $data['name'],
            'last_name' => $data['last_name'],
        ]);
    }

    public function updateAvatar(User $user, UploadedFile $file): User
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = "users/user_{$user->id}";
        $filename = 'avatar.' . $file->getClientOriginalExtension();
        $file->storeAs($path, $filename, 'public');

        return $this->users->update($user, ['avatar' => "$path/$filename"]);
    }

    public function deleteAvatar(User $user): User
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        return $this->users->update($user, ['avatar' => null]);
    }

    public function updateEmail(User $user, string $email, string $password, ?string $code, ?string $recoveryCode): User
    {
        $this->assertIdentityConfirmed($user, $password, $code, $recoveryCode);

        $previousEmail = $user->email;
        $updated = $this->users->update($user, ['email' => $email]);

        // Se avisa a la dirección anterior, no a la nueva — así el dueño real
        // de la cuenta se entera aunque ya no pueda entrar con ese correo.
        Notification::route('mail', $previousEmail)
            ->notify(new EmailChangedNotification($updated, $previousEmail, $email));

        return $updated;
    }

    public function updatePassword(User $user, ?string $currentPassword, string $newPassword, ?string $code, ?string $recoveryCode): User
    {
        $this->assertIdentityConfirmed($user, $currentPassword, $code, $recoveryCode);

        $user->password = Hash::make($newPassword);
        $user->has_usable_password = true;
        $user->save();

        $currentTokenId = $user->currentAccessToken() instanceof PersonalAccessToken
            ? $user->currentAccessToken()->id
            : null;

        $user->tokens()->when($currentTokenId, fn ($query) => $query->where('id', '!=', $currentTokenId))->delete();

        $user->notify(new PasswordChangedNotification());

        return $user;
    }

    /**
     * Vincula una cuenta social a un usuario ya autenticado. No pide
     * confirmación de identidad — mismo criterio que enable2FA: agregar un
     * método de acceso es de menor riesgo que quitarlo o cambiarlo, y la
     * sesión activa ya es la prueba de identidad.
     */
    public function linkSocialAccount(User $user, string $provider, string $idToken): User
    {
        $claims = $this->authService->verifySocialClaims($provider, $idToken);

        $existing = $this->users->findBySocialProvider($provider, $claims['sub']);

        if ($existing && $existing->id !== $user->id) {
            throw new ProfileException('Esta cuenta de Google ya está vinculada a otro usuario.', 422);
        }

        if (! $existing) {
            $this->users->linkSocialAccount($user, $provider, $claims['sub']);
        }

        return $user->load('socialAccounts');
    }

    /**
     * Desvincula una cuenta social — bloquea si es la única forma de acceso
     * del usuario (sin contraseña real y sin ningún otro proveedor), y si no,
     * exige la misma confirmación de identidad que updateEmail/updatePassword.
     */
    public function unlinkSocialAccount(User $user, string $provider, ?string $password, ?string $code, ?string $recoveryCode): User
    {
        $user->loadMissing('socialAccounts');

        $remainingProviders = $user->socialAccounts->where('provider', '!=', $provider)->count();

        if (! $user->has_usable_password && $remainingProviders === 0) {
            throw new ProfileException(
                'No podés desvincular tu única forma de acceso. Configurá una contraseña primero.',
                422
            );
        }

        $this->assertIdentityConfirmed($user, $password, $code, $recoveryCode);

        $this->users->unlinkSocialAccount($user, $provider);

        return $user->load('socialAccounts');
    }

    private function assertIdentityConfirmed(User $user, ?string $password, ?string $code, ?string $recoveryCode): void
    {
        if ($user->has_usable_password && (! $password || ! Hash::check($password, $user->password))) {
            throw new ProfileException('Contraseña incorrecta.', 403);
        }

        if ($user->two_factor_status && ! $this->authService->verifyTwoFactorCode($user, $code, $recoveryCode)) {
            throw new ProfileException('Código de autenticación inválido.', 403);
        }
    }
}
