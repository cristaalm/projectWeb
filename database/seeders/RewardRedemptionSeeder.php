<?php

namespace Database\Seeders;

use App\Models\RewardRedemption;
use App\Enums\RewardRedemptionStatus;
use Illuminate\Database\Seeder;

class RewardRedemptionSeeder extends Seeder
{
    public function run(): void
    {
        RewardRedemption::factory(50)
            ->state([
                'status' => fake()->randomElement(RewardRedemptionStatus::cases())->value,
            ])
            ->create();
    }
}
