<?php

namespace Database\Factories;

use App\Models\IdentityVerification;
use App\Models\User;
use App\Models\Role;
use App\Enums\IndentifyVerificationStatus; // Asegúrate del nombre correcto
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class IdentityVerificationFactory extends Factory
{
    protected $model = IdentityVerification::class;

    public function definition(): array
    {
        return [
            // Usuario regular que necesita verificación
            'user_id' => fn() => User::whereHas('role', fn($q) => $q->where('name', 'user'))
                ->inRandomOrder()
                ->first()?->id
                ?? User::factory()->create(['role_id' => function () {
                    return Role::firstWhere('name', 'user')?->id ??
                           Role::factory()->create(['name' => 'user', 'display_name' => 'Usuario solicitante'])->id;
                }])->id,

            'ine_front_url' => 'ine_front/' . Str::random(10) . '.jpg',
            'ine_back_url' => 'ine_back/' . Str::random(10) . '.jpg',
            'document_number' => $this->faker->unique()->numerify('##########'),
            'status' => $this->faker->randomElement(IndentifyVerificationStatus::cases())->value,
            'rejection_reason' => $this->faker->optional()->sentence(),

            // Admin que verifica (debe tener rol 'admin')
            'verified_by' => fn() => User::whereHas('role', fn($q) => $q->where('name', 'admin'))
                ->inRandomOrder()
                ->first()?->id
                ?? User::factory()->create([
                    'name' => 'Admin Verificador',
                    'email' => 'verifier_' . Str::random(5) . '@example.com',
                    'role_id' => Role::firstWhere('name', 'admin')?->id ??
                                Role::factory()->create(['name' => 'admin', 'display_name' => 'Administrador'])->id,
                ])->id,

            'verified_at' => $this->faker->optional(0.7)->dateTimeThisYear(), // 70% probabilidad de tener fecha
        ];
    }
}
