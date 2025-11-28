<?php

namespace Database\Factories;

use App\Models\TypeShop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TypeShopFactory extends Factory
{
    protected $model = TypeShop::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(150),
        ];
    }
}
