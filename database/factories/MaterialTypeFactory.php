<?php

namespace Database\Factories;

use App\Models\MaterialType;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialTypeFactory extends Factory
{
    protected $model = MaterialType::class;

    public function definition(): array
    {
        $names = ['Plástico', 'Aluminio', 'Otros'];
        $slugs = ['plastic', 'aluminum', 'other'];
        $points = [15, 35, 0];

        $index = $this->faker->numberBetween(0, 2);

        return [
            'name' => $names[$index],
            'slug' => $slugs[$index],
            'points' => $points[$index],
            'is_active' => true,
            'description' => $this->faker->sentence(),
        ];
    }
}
