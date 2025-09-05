<?php

namespace Database\Factories;

use App\Models\Alliance;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\AllianceStatus;
use Illuminate\Support\Str;


class AllianceFactory extends Factory
{
    protected $model = Alliance::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'logo_url' => 'logos/' . Str::random(10) . '.png',
            'contact_name' => $this->faker->name(),
            'contact_email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'status' => $this->faker->randomElement([
                AllianceStatus::ACTIVE,
                AllianceStatus::PAUSED,
            ]),
        ];
    }
}
