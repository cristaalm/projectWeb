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
        $user = null;
        $comerciant = null;

        // no tenga el rol 4
        $user = User::where('role_id', '!=', 4)->inRandomOrder()->first()?->id;

        if ( $type == 1 ) { // si es canjeo 
            $alliance = Alliance::inRandomOrder()->first()?->id
                ?? Alliance::factory()->create()->id;

            $reward = Reward::select('id', 'points_required')->inRandomOrder()->first()
                ?? Reward::factory()->create();

            // elejimos aleatoriamente 50 / 50, si va a haber comerciant_id
            $comerciant = $this->faker->randomElement([true, false]) ? User::where('role_id', 4)->inRandomOrder()->first()?->id : null;

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
            'user_id' => $user,
            'comerciant_id' => $comerciant,
            'type_history' => $type,
            'alliance_id' => $alliance,
            'material_type_id' => $material_type,
            'reward_id' => $reward_id,
            'points' => $points,
            'scan_id' => $scan_id,
        ];
    }
}
