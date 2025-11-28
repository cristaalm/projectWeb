<?php

namespace Database\Factories;

use App\Models\EnvironmentalMetric;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnvironmentalMetricFactory extends Factory
{
    protected $model = EnvironmentalMetric::class;

    public function definition(): array
    {
        return [
            'date' => $this->faker->unique()->date(),
            'total_users' => $this->faker->numberBetween(10, 1000),
            'total_scans' => $this->faker->numberBetween(50, 5000),
            'total_valid_scans' => $this->faker->numberBetween(30, 4500),
            'total_points_awarded' => $this->faker->numberBetween(100, 10000),
            'kg_recycled' => $this->faker->randomFloat(2, 1, 100),
            'co2_saved_kg' => $this->faker->randomFloat(2, 3, 300),
        ];
    }
}
