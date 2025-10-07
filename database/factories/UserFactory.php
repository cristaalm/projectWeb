<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {

        $digits12 = implode('', $this->faker->randomElements(range('0', '9'), 12, true));
        $checkDigit = User::calculateEan13CheckDigit($digits12);

        return [
            'name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'curp' => $this->faker->unique()->bothify('????????????????'),
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'total_points' => $this->faker->numberBetween(0, 500),
            'verification_status' => $this->faker->randomElement(VerificationStatus::cases())->value,
            'status' => UserStatus::ACTIVE->value,
            'two_factor_status' => 0,
            'google2fa_secret' => (new Google2FA())->generateSecretKey(),
            'code_identity'=> $digits12 . $checkDigit,
            'role_id' => function (array $attributes) {
                return Role::firstWhere('name', 'user')?->id ??
                       Role::factory()->create(['name' => 'user', 'display_name' => 'Usuario solicitante'])->id;
            },
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserStatus::INACTIVE->value,
        ]);
    }

    public function pendingVerification(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => VerificationStatus::PENDING->value,
        ]);
    }

    public function approvedVerification(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => VerificationStatus::APPROVED->value,
        ]);
    }
}
