<?php

namespace Database\Seeders;

use App\Models\Scan;
use Illuminate\Database\Seeder;

class ScanSeeder extends Seeder
{
    public function run(): void
    {
        Scan::factory(200)->create();
    }
}
