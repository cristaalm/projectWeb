<?php

namespace Database\Seeders;

use App\Models\MaterialType;
use Illuminate\Database\Seeder;

class MaterialTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Plástico', 'slug' => 'plastic', 'points' => 10],
            ['name' => 'Vidrio', 'slug' => 'glass', 'points' => 15],
            ['name' => 'Lata', 'slug' => 'can', 'points' => 5],
            ['name' => 'Cartón', 'slug' => 'cardboard', 'points' => 8],
            ['name' => 'Basura', 'slug' => 'garbage', 'points' => 0],
        ];

        foreach ($types as $type) {
            MaterialType::firstOrCreate(['slug' => $type['slug']], $type);
        }
    }
}
