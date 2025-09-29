<?php

namespace Database\Factories;

use App\Models\History;
use App\Models\MaterialType;
use App\Models\Alliance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HistoryFactory extends Factory
{
    protected $model = History::class;

    public function definition(): array
    {

        $type = $this->faker->randomElement([1, 2]); // 1 = canjeo, 2 = suma
        $alliance = null;
        $material_type = null;
        $points = 0;

        if ( $type == 1 ) { // si es canjeo 
            $alliance = Alliance::inRandomOrder()->first()?->id
                ?? Alliance::factory()->create()->id;

            // numero negativo
            $points = $this->faker->numberBetween(-100, -1);
        } else { // si es suma
            $material_type = MaterialType::inRandomOrder()->where('points', '>', 0)->first()?->id
                ?? MaterialType::factory()->create()->id;

            $points = $this->faker->numberBetween(1, 100);
        }

        return [
            // Reutiliza un usuario existente (aleatorio)
            'user_id' => fn() => User::inRandomOrder()->first()?->id
                ?? User::factory()->create()->id, // Crea uno solo si NO hay usuarios

            'type_history' => $type,
            'alliance_id' => $alliance,
            'material_type_id' => $material_type,
            'points' => $points,
        ];
    }
}
