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
            AllianceSeeder::class,
            MaterialTypeSeeder::class,
            ContainerSeeder::class,
            ScanSeeder::class,
            RewardSeeder::class,
            RewardRedemptionSeeder::class,
            IdentityVerificationSeeder::class,
            NotificationSeeder::class,
            EnvironmentalMetricSeeder::class,
        ]);
    }
}
