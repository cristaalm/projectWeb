<?php

namespace Database\Seeders;

use App\Models\Scan;
use Illuminate\Database\Seeder;
use App\Enums\ScanStatus;

class ScanSeeder extends Seeder
{
    public function run(): void
    {
        // Scan::factory(200)->create();
        Scan::factory(5000)
        ->state([
            'user_id' => 2,
            'scan_status' => ScanStatus::SUCCESS->value,
            'is_valid' => fake()->randomElement([
                true,
                false,
            ]),
        ])
        ->create();
    }
}
