<?php

namespace Database\Seeders;

use App\Models\MaterialTypes;
use Illuminate\Database\Seeder;

class MaterialTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Plástico', 'slug' => 'plastic', 'points' => 15],
            ['name' => 'Aluminio', 'slug' => 'aluminum', 'points' => 35],
            ['name' => 'Otros', 'slug' => 'other', 'points' => 0],
        ];

        foreach ($types as $type) {
            MaterialTypes::firstOrCreate(['slug' => $type['slug']], $type);
        }
    }
}
