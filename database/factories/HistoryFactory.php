<?php

namespace Database\Factories;

use App\Models\History;
use App\Models\MaterialTypes;
use App\Models\Alliance;
use App\Models\User;
use App\Models\Reward;
use App\Models\Scan;
use Illuminate\Database\Eloquent\Factories\Factory;

class HistoryFactory extends Factory
{
    protected $model = History::class;

    public function definition(): array
    {

        $type = $this->faker->randomElement([1, 2]); // 1 = canjeo, 2 = suma
        $alliance = null;
        $reward_id = null;
        $material_type = null;
        $scan_id = null;
        $points = 0;

        if ( $type == 1 ) { // si es canjeo 
            $alliance = Alliance::inRandomOrder()->first()?->id
                ?? Alliance::factory()->create()->id;

            $reward = Reward::select('id', 'points_required')->inRandomOrder()->first()
                ?? Reward::factory()->create();

            $reward_id = $reward->id;

            $points = $reward->points_required * -1;
        } else { // si es suma
            $material_type = MaterialTypes::inRandomOrder()->where('points', '>', 0)->first()?->id
                ?? MaterialTypes::factory()->create()->id;

                
            $scan = Scan::where('material_type_id', $material_type)->inRandomOrder()->first()
                ?? Scan::factory()->create();
            
            $points = $scan->points_awarded;
            
            $scan_id = $scan->id;
        }

        return [
            // Reutiliza un usuario existente (aleatorio)
            'user_id' => fn() => User::inRandomOrder()->first()?->id
                ?? User::factory()->create()->id, // Crea uno solo si NO hay usuarios

            'type_history' => $type,
            'alliance_id' => $alliance,
            'material_type_id' => $material_type,
            'reward_id' => $reward_id,
            'points' => $points,
            'scan_id' => $scan_id,
        ];
    }
}
