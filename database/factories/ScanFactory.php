<?php

namespace Database\Factories;

use App\Models\Scan;
use App\Models\User;
use App\Models\Container;
use App\Models\MaterialTypes;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\ScanStatus;
use Illuminate\Support\Str;

class ScanFactory extends Factory
{
    protected $model = Scan::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(ScanStatus::cases());
    
        // obtenemos un tipo de material
        $materialType = MaterialTypes::inRandomOrder()->first();
        
        // obtenemos si esta aplastado o no
        $isCrushed = $this->faker->boolean();

        return [
            'user_id' => fn() => User::inRandomOrder()->first()?->id ??
                       User::factory()->create()->id,

            'container_id' => fn() => Container::inRandomOrder()->first()?->id ??
                       Container::factory()->create()->id,

            'material_type_id' => $materialType->id,
                       
            'image' => '' . Str::random(15) . '.jpg',
            'scan_status' => $status->value,
            'is_valid' => $status === ScanStatus::SUCCESS,
            'is_crushed' => $status === ScanStatus::SUCCESS ? $isCrushed : false,
            'points_awarded' => $status === ScanStatus::SUCCESS ? $materialType->points + ($isCrushed ? 5 : 0) : 0,
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
