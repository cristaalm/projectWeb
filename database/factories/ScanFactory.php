<?php

namespace Database\Factories;

use App\Models\Scan;
use App\Models\User;
use App\Models\Container;
use App\Models\MaterialType;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\ScanStatus;
use Illuminate\Support\Str;

class ScanFactory extends Factory
{
    protected $model = Scan::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(ScanStatus::cases());
    
        return [
            'user_id' => fn() => User::inRandomOrder()->first()?->id ??
                       User::factory()->create()->id,

            'container_id' => fn() => Container::inRandomOrder()->first()?->id ??
                       Container::factory()->create()->id,

            'material_type_id' => fn() => MaterialType::inRandomOrder()->first()?->id ??
                       MaterialType::factory()->create()->id,
                       
            'image' => '' . Str::random(15) . '.jpg',
            'scan_status' => $status->value,
            'is_valid' => $status === ScanStatus::SUCCESS,
            'points_awarded' => $status === ScanStatus::SUCCESS ? $this->faker->numberBetween(5, 15) : 0,
            'description' => $status === ScanStatus::FAILED
                ? $this->faker->randomElement([
                    'Material no reconocido',
                    'Imagen borrosa',
                    'Objeto no reciclable',
                    'Luz insuficiente',
                    'Material contaminado',
                  ])
                : $this->faker->sentence(),
            'scanned_at' => $this->faker->dateTimeThisMonth(),
        ];
    }
}
