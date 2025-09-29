<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TypeShopSeeder::class,
            AllianceSeeder::class,
            MaterialTypeSeeder::class,
            ContainerSeeder::class,
            ScanSeeder::class,
            RewardSeeder::class,
            RewardsUserSeeder::class,
            IdentityVerificationSeeder::class,
            HistorySeeder::class,
            EnvironmentalMetricSeeder::class,
        ]);
    }
}
