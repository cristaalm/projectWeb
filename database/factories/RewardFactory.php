<?php

namespace Database\Factories;

use App\Models\Reward;
use App\Models\Alliance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RewardFactory extends Factory
{
    protected $model = Reward::class;

    public function definition(): array
    {
        return [
            // Reutiliza una alianza existente (aleatoria)
            'alliance_id' => fn() => Alliance::inRandomOrder()->first()?->id
                ?? Alliance::factory()->create()->id, // Crea una solo si no hay

            'name' => $this->faker->randomElement([
                'Descuento 10% en Supermercado X',
                '2x1 en comida rápida',
                'Entrada gratis al cine',
                'Café cortesía',
                'Litro de leche gratis',
                'Galletas de regalo',
                'Descuento en frutas',
            ]),

            'description' => $this->faker->paragraph(),

            'points_required' => $this->faker->numberBetween(50, 500),

            'image_url' => 'rewards/' . Str::random(10) . '.jpg',

            // 70% de probabilidad de tener stock limitado
            'stock' => $this->faker->optional(0.7)->numberBetween(1, 100),

            'is_active' => $this->faker->boolean(90), // 90% activas

            // 50% de probabilidad de tener fecha de expiración
            'expires_at' => $this->faker->optional(0.5)->dateTimeBetween('now', '+1 year'),
        ];
    }
}
