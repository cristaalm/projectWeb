<?php

namespace Database\Seeders;

use App\Models\EnvironmentalMetric;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EnvironmentalMetricSeeder extends Seeder
{
    public function run(): void
    {
        $days = 30;
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i);

            EnvironmentalMetric::firstOrCreate(
                ['date' => $date->toDateString()],
                [
                    'total_users' => rand(50, 500),
                    'total_scans' => rand(100, 1000),
                    'total_valid_scans' => rand(80, 900),
                    'total_points_awarded' => rand(500, 10000),
                    'kg_recycled' => round(rand(50, 500) / 10, 2),
                    'co2_saved_kg' => round(rand(150, 1500) / 10, 2),
                ]
            );
        }
    }
}
