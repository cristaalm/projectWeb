<?php

namespace Database\Factories;

use App\Models\RewardRedemption;
use App\Models\User;
use App\Models\Reward;
use App\Enums\RewardRedemptionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class RewardRedemptionFactory extends Factory
{
    protected $model = RewardRedemption::class;

    public function definition(): array
    {
        return [
            // Reutiliza un usuario existente
            'user_id' => fn() => User::inRandomOrder()->first()?->id
                ?? User::factory()->create()->id,

            // Admin que autoriza/entrega
            'redeemed_by' => fn() => User::whereHas('role', fn($q) => $q->where('name', 'admin'))
                ->inRandomOrder()
                ->first()?->id
                ?? User::factory()->create([
                    'name' => 'Admin Canje',
                    'email' => 'admin-canje-' . \Illuminate\Support\Str::random(5) . '@example.com',
                    'role_id' => fn() => \App\Models\Role::firstWhere('name', 'admin')?->id ??
                                      \App\Models\Role::factory()->create(['name' => 'admin'])->id,
                ])->id,

            // Usa una recompensa existente
            'reward_name' => fn() => $this->getReward()->name,
            'reward_image_url' => fn() => $this->getReward()->image_url,
            'points_used' => fn() => $this->getReward()->points_required,

            // Estado aleatorio
            'status' => $this->faker->randomElement(RewardRedemptionStatus::cases())->value,

            // Expira en 30 días (puede ajustarse por estado)
            'expires_at' => now()->addDays(30),

            // Fecha de canje: en el mes actual
            'redeemed_at' => $this->faker->dateTimeThisMonth(),
        ];
    }

    // Método auxiliar para obtener una recompensa existente
    protected function getReward(): Reward
    {
        $reward = Reward::inRandomOrder()->first();

        if (! $reward) {
            // Si no hay recompensas, crea una (solo una)
            $alliance = \App\Models\Alliance::inRandomOrder()->first()
                ?? \App\Models\Alliance::factory()->create();

            $reward = Reward::factory()->create(['alliance_id' => $alliance->id]);
        }

        return $reward;
    }

    // Estados específicos
    public function delivered(): static
    {
        return $this->state([
            'status' => RewardRedemptionStatus::DELIVERED->value,
            'redeemed_at' => now()->subDays(rand(1, 10)), // Entregado hace días
        ]);
    }

    public function cancelled(): static
    {
        return $this->state([
            'status' => RewardRedemptionStatus::CANCELLED->value,
            'redeemed_at' => now()->subDays(rand(1, 5)),
            'expires_at' => now()->addDays(10), // Podría haber expirado después
        ]);
    }

    public function expired(): static
    {
        return $this->state([
            'status' => RewardRedemptionStatus::EXPIRED->value,
            'expires_at' => now()->subDays(rand(1, 10)), // Ya expiró
            'redeemed_at' => now()->subDays(rand(15, 30)),
        ]);
    }

    public function redeemed(): static
    {
        return $this->state([
            'status' => RewardRedemptionStatus::REDEEMED->value,
        ]);
    }
}
