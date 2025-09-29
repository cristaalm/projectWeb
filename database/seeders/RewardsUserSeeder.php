<?php

namespace Database\Seeders;

use App\Models\RewardsUser;
use Illuminate\Database\Seeder;

class RewardsUserSeeder extends Seeder
{
    public function run(): void
    {
        RewardsUser::factory(320)->create();
    }
}
