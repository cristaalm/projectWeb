<?php

namespace Database\Seeders;

use App\Models\Container;
use Illuminate\Database\Seeder;
use App\Enums\ContainerStatus;

class ContainerSeeder extends Seeder
{
    public function run(): void
    {
        Container::factory(10)->state([
            'status' => ContainerStatus::ACTIVE,
        ])->create();
        Container::factory(10)->state([
            'status' => ContainerStatus::INACTIVE,
        ])->create();
    }
}
