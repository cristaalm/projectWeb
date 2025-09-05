<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            // Reutiliza un usuario existente (aleatorio)
            'user_id' => fn() => User::inRandomOrder()->first()?->id
                ?? User::factory()->create()->id, // Crea uno solo si NO hay usuarios

            'type' => $this->faker->randomElement(['scan_rejected', 'points_awarded', 'reward_canjeado', 'welcome']),
            'title' => $this->faker->sentence(6),
            'message' => $this->faker->paragraph(),
            'is_read' => $this->faker->boolean(60), // 60% de probabilidad de estar leído
            'action_url' => $this->faker->optional()->url(), // 50% de probabilidad de tener URL
        ];
    }
}
