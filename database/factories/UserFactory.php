<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'total_points' => $this->faker->numberBetween(0, 500),
            'verification_status' => $this->faker->randomElement(VerificationStatus::cases())->value,
            'status' => UserStatus::ACTIVE->value,
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
