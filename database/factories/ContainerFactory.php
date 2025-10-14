<?php

namespace Database\Factories;

use App\Models\Container;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\ContainerStatus;
use Illuminate\Support\Str;

class ContainerFactory extends Factory
{
    protected $model = Container::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'serial_number' => 'SN-' . strtoupper(Str::random(12)),
            'location' => $this->faker->address(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'capacity' => $this->faker->numberBetween(1, 100),
            'status' => $this->faker->randomElement([
                ContainerStatus::ACTIVE,
                ContainerStatus::INACTIVE,
            ]),
        ];
    }
}
