<?php

namespace Database\Factories;

use App\Models\IdentityVerification;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Enums\VerificationStatus;
use App\Enums\UserStatus;

class IdentityVerificationFactory extends Factory
{
    protected $model = IdentityVerification::class;

    public function definition(): array
    {

        // Admin para verified_by
        $adminRole = Role::firstOrCreate(['name' => 'admin'], [
            'display_name' => 'Administrador',
            'is_active' => true,
        ]);

        $verifiedBy = User::whereHas('role', fn($q) => $q->where('name', 'admin'))
            ->inRandomOrder()
            ->first()?->id
            ?? User::factory()->create([
                'name' => 'Admin',
                'last_name' => 'System',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
                'google2fa_secret' => 'SECRET123',
                'two_factor_status' => true,
                'verification_status' => VerificationStatus::APPROVED->value,
                'status' => UserStatus::ACTIVE->value,
            ])->id;

        return [
            // 'user_id' se debe pasar desde afuera
            'ine_front_url' => null, // Se actualizará en afterCreating
            'ine_back_url' => null,
            'selfie_url' => null,
            'status' => VerificationStatus::PENDING->value,
            'rejection_reason' => null,
            'verified_by' => $verifiedBy,
            'verified_at' => null,
        ];
    }

    // 👇 Método para asociar un usuario EXISTENTE
    public function forUser(User $user): static
    {
        return $this->state(['user_id' => $user->id])
                    ->afterCreating(function (IdentityVerification $iv) use ($user) {
                        // Actualizamos las URLs basadas en el estado del usuario
                        if ($user->verification_status->value == VerificationStatus::EMPTY->value) {
                            // No hay documentos
                            $iv->update([
                                'ine_front_url' => null,
                                'ine_back_url' => null,
                                'selfie_url' => null,
                                'status' => VerificationStatus::EMPTY->value,
                            ]);
                        } else {
                            // Hay documentos
                            $iv->update([
                                'ine_front_url' => 'users/user_' . $user->id . '/ine_front.jpg',
                                'ine_back_url' => 'users/user_' . $user->id . '/ine_back.jpg',
                                'selfie_url' => 'users/user_' . $user->id . '/selfie.jpg',
                                'status' => $user->verification_status->value,
                            ]);
                        }
                    });
    }
}
