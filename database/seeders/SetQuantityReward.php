<?php

namespace Database\Seeders;

use App\Models\RewardsUser;
use App\Models\History;
use Illuminate\Database\Seeder;
use Faker\Factory;

class SetQuantityReward extends Seeder
{
    public function run()
    {
        $faker = Factory::create();

        foreach (RewardsUser::cursor() as $reward) {
            $quantity = $faker->randomElement([1, 2, 3]);
            $reward->quantity = $quantity;
            $reward->save();
        }

        foreach (History::where('type_history', 1)->cursor() as $history) {
            $quantity = $faker->randomElement([1, 2, 3]);
            $history->quantity = $quantity;
            $history->save();
        }
    }
}
