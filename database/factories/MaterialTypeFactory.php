<?php

namespace Database\Factories;

use App\Models\MaterialType;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialTypeFactory extends Factory
{
    protected $model = MaterialType::class;

    public function definition(): array
    {
        $names = ['Plástico', 'Vidrio', 'Lata', 'Cartón', 'Basura'];
        $slugs = ['plastic', 'glass', 'can', 'cardboard', 'garbage'];
        $points = [10, 15, 5, 8, 0];

        $index = $this->faker->numberBetween(0, 4);

        return [
            'name' => $names[$index],
            'slug' => $slugs[$index],
            'points' => $points[$index],
            'is_active' => true,
            'description' => $this->faker->sentence(),
        ];
    }
}
