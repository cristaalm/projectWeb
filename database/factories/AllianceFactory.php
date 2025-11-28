<?php

namespace Database\Factories;

use App\Models\Alliance;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\AllianceStatus;
use Illuminate\Support\Str;
use App\Models\TypeShop;


class AllianceFactory extends Factory
{
    protected $model = Alliance::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'logo' => false,
            'ext' => null,
            'contact_name' => $this->faker->name(),
            'contact_email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'type_shop_id' => fn() => TypeShop::inRandomOrder()->first()?->id ?? TypeShop::factory()->create()->id,
            'status' => $this->faker->randomElement([
                AllianceStatus::ACTIVE,
                AllianceStatus::PAUSED,
            ]),
        ];
    }
}
