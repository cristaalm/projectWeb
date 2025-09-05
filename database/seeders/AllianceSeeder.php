<?php

namespace Database\Seeders;

use App\Models\Alliance;
use Illuminate\Database\Seeder;
use App\Enums\AllianceStatus;

class AllianceSeeder extends Seeder
{
    public function run(): void
    {
        Alliance::factory(5)->state([
            'status' => AllianceStatus::ACTIVE->value,
        ])->create();
        Alliance::factory(5)->state([
            'status' => AllianceStatus::PAUSED->value,
        ])->create();
    }
}
