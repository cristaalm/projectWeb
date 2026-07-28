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

    public function updatePassword(User $user, string $currentPassword, string $newPassword, ?string $code, ?string $recoveryCode): void
    {
        $this->assertIdentityConfirmed($user, $currentPassword, $code, $recoveryCode);

        $user->password = Hash::make($newPassword);
        $user->save();

        $currentTokenId = $user->currentAccessToken() instanceof PersonalAccessToken
            ? $user->currentAccessToken()->id
            : null;

        $user->tokens()->when($currentTokenId, fn ($query) => $query->where('id', '!=', $currentTokenId))->delete();

        $user->notify(new PasswordChangedNotification());
    }

    private function assertIdentityConfirmed(User $user, string $password, ?string $code, ?string $recoveryCode): void
    {
        if (! Hash::check($password, $user->password)) {
            throw new ProfileException('Contraseña incorrecta.', 403);
        }

        if ($user->two_factor_status && ! $this->authService->verifyTwoFactorCode($user, $code, $recoveryCode)) {
            throw new ProfileException('Código de autenticación inválido.', 403);
        }
    }
}
