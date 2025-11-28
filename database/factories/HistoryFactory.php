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

    // public function definition(): array
    // {

    //     $type = $this->faker->randomElement([1, 2, 3, 4]); // 1 = canjeo, 2 = suma, 3 = modificación de puntos, 4 = corte de caja
    //     $alliance = null;
    //     $reward_id = null;
    //     $material_type = null;
    //     $scan_id = null;
    //     $points = 0;
    //     $user = null;
    //     $comerciant = null;
    //     $quantity = null;

    //     // no tenga el rol 4
    //     $user = User::where('role_id', '!=', 4)->inRandomOrder()->first()?->id;

    //     if ( $type == 1 ) { // si es canjeo 
    //         $alliance = Alliance::inRandomOrder()->first()?->id
    //             ?? Alliance::factory()->create()->id;

    //         $reward = Reward::select('id', 'points_required')->inRandomOrder()->first()
    //             ?? Reward::factory()->create();

    //         // elejimos aleatoriamente 50 / 50, si va a haber comerciant_id
    //         $comerciant = $this->faker->randomElement([true, false]) ? User::where('role_id', 4)->inRandomOrder()->first()?->id : null;

    //         $reward_id = $reward->id;

    //         $quantity = $this->faker->randomElement([1, 2, 3]);

    //         $points = $reward->points_required * $quantity;
    //         $points *= -1;
    //     } else if ( $type == 2 ) { // si es suma
    //         $material_type = MaterialTypes::inRandomOrder()->where('points', '>', 0)->first()?->id
    //             ?? MaterialTypes::factory()->create()->id;

    //         $scan = Scan::where('material_type_id', $material_type)->inRandomOrder()->first()
    //             ?? Scan::factory()->create();
            
    //         $points = $scan->points_awarded;
            
    //         $scan_id = $scan->id;
    //     } else if ( $type == 3 ) { // si es modificación de puntos
    //         // obtenemos un usuario aleatorio
    //         $user = User::inRandomOrder()->first()?->id
    //             ?? User::factory()->create()->id;

    //         // cambiamos sus puntos por una cantidad aleatoria, entre 1000 y 80000
    //         $points = $this->faker->randomNumber(5, true);
    //     } else if ( $type == 4 ) { // si es corte de caja
    //         $alliance = Alliance::inRandomOrder()->first()?->id
    //             ?? Alliance::factory()->create()->id;
            
    //         $points = $alliance->total_points;
            
    //         $alliance_id = $alliance->id;
    //     }
            

    //     return [
    //         // Reutiliza un usuario existente (aleatorio)
    //         'user_id' => $user,
    //         'comerciant_id' => $comerciant,
    //         'type_history' => $type,
    //         'scan_id' => $scan_id,
    //         'material_type_id' => $material_type,
    //         'description' => $type == 3 ? $this->faker->sentence : null,
    //         'reward_id' => $reward_id,
    //         'points' => $points,
    //         'quantity' => $quantity,
    //         'alliance_id' => $alliance,
    //     ];
    // }

    public function definition(): array
    {

        $type = 2; // 1 = canjeo, 2 = suma, 3 = modificación de puntos, 4 = corte de caja
        $alliance = null;
        $reward_id = null;
        $material_type = null;
        $scan_id = null;
        $points = 0;
        $user = null;
        $comerciant = null;
        $quantity = null;

        // no tenga el rol 4
        $user = 1;

        $material_type = MaterialTypes::inRandomOrder()->where('points', '>', 0)->first()?->id
            ?? MaterialTypes::factory()->create()->id;

        $scan = Scan::where('material_type_id', $material_type)->inRandomOrder()->first()
            ?? Scan::factory()->create();
        
        $points = $scan->points_awarded;
        
        $scan_id = $scan->id;
            

        return [
            // Reutiliza un usuario existente (aleatorio)
            'user_id' => $user,
            'comerciant_id' => null,
            'type_history' => $type,
            'scan_id' => $scan_id,
            'material_type_id' => $material_type,
            'description' => null,
            'reward_id' => null,
            'points' => $points,
            'quantity' => null,
            'alliance_id' => null,
        ];
    }
}
