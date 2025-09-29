<?php

namespace Database\Seeders;

use App\Models\Scan;
use Illuminate\Database\Seeder;
use App\Enums\ScanStatus;

class ScanSeeder extends Seeder
{
    public function run(): void
    {
        Scan::factory(2000)->create();
    }
}
